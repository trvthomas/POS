/* *
 *
 *  License: www.highcharts.com/license
 *  Author: Torstein Honsi, Christer Vasseng
 *
 *  This module serves as a fallback for the Boost module in IE9 and IE10. Newer
 *  browsers support WebGL which is faster.
 *
 *  It is recommended to include_once this module in conditional comments targeting
 *  IE9 and IE10.
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 * */
'use strict';
import BoostChart from './Boost/BoostChart.js';
const { getBoostClipRect, isChartSeriesBoosting } = BoostChart;
import BoostSeries from './Boost/BoostSeries.js';
const { destroyGraphics } = BoostSeries;
import Chart from '../Core/Chart/Chart.js';
import Color from '../Core/Color/Color.js';
const { parse: color } = Color;
import H from '../Core/Globals.js';
const { doc, noop } = H;
import Series from '../Core/Series/Series.js';
import SeriesRegistry from '../Core/Series/SeriesRegistry.js';
const { seriesTypes } = SeriesRegistry;
import U from '../Core/Utilities.js';
const { addEvent, extend, fireEvent, isNumber, merge, pick, wrap } = U;
// Use a blank pixel for clearing canvas (#17182)
const b64BlankPixel = (
/* eslint-disable-next-line max-len */
'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
let CHUNK_SIZE = 50000, destroyLoadingDiv;
/* eslint-disable no-invalid-this, valid-jsdoc */
/**
 * Initialize the canvas boost.
 *
 * @function Highcharts.initCanvasBoost
 */
const initCanvasBoost = function () {
    if (H.seriesTypes.heatmap) {
        wrap(H.seriesTypes.heatmap.prototype, 'drawPoints', function () {
            const chart = this.chart, ctx = this.getContext(), inverted = this.chart.inverted, xAxis = this.xAxis, yAxis = this.yAxis;
            if (ctx) {
                // draw the columns
                this.points.forEach(function (point) {
                    let plotY = point.plotY, pointAttr;
                    if (typeof plotY !== 'undefined' &&
                        !isNaN(plotY) &&
                        point.y !== null &&
                        ctx) {
                        const { x = 0, y = 0, width = 0, height = 0 } = point.shapeArgs || {};
                        if (!chart.styledMode) {
                            pointAttr = point.series.pointAttribs(point);
                        }
                        else {
                            pointAttr = point.series.colorAttribs(point);
                        }
                        ctx.fillStyle = pointAttr.fill;
                        if (inverted) {
                            ctx.fillRect(yAxis.len - y + xAxis.left, xAxis.len - x + yAxis.top, -height, -width);
                        }
                        else {
                            ctx.fillRect(x + xAxis.left, y + yAxis.top, width, height);
                        }
                    }
                });
                this.canvasToSVG();
            }
            else {
                this.chart.showLoading('Your browser doesn\'t support HTML5 canvas, <br>' +
                    'please use a modern browser');
            }
        });
    }
    extend(Series.prototype, {
        /**
         * Create a hidden canvas to draw the graph on. The contents is later
         * copied over to an SVG image element.
         *
         * @private
         * @function Highcharts.Series#getContext
         */
        getContext: function () {
            const chart = this.chart, target = isChartSeriesBoosting(chart) ? chart : this, targetGroup = (target === chart ?
                chart.seriesGroup :
                chart.seriesGroup || this.group);
            let width = chart.chartWidth, height = chart.chartHeight, ctx, swapXY = function (proceed, x, y, a, b, c, d) {
                proceed.call(this, y, x, a, b, c, d);
            };
            const boost = target.boost =
                target.boost ||
                    {};
            ctx = boost.targetCtx;
            if (!boost.canvas) {
                boost.canvas = doc.createElement('canvas');
                boost.target = chart.renderer
                    .image('', 0, 0, width, height)
                    .addClass('highcharts-boost-canvas')
                    .add(targetGroup);
                ctx = boost.targetCtx =
                    boost.canvas.getContext('2d');
                if (chart.inverted) {
                    ['moveTo', 'lineTo', 'rect', 'arc'].forEach((fn) => {
                        wrap(ctx, fn, swapXY);
                    });
                }
                boost.copy = function () {
                    boost.target.attr({
                        href: boost.canvas.toDataURL('image/png')
                    });
                };
                boost.clear = function () {
                    ctx.clearRect(0, 0, boost.canvas.width, boost.canvas.height);
                    if (target === boost.target) {
                        boost.target.attr({
                            href: b64BlankPixel
                        });
                    }
                };
                boost.clipRect = chart.renderer.clipRect();
                boost.target.clip(boost.clipRect);
            }
            else if (!(target instanceof Chart)) {
                // ctx.clearRect(0, 0, width, height);
            }
            if (boost.canvas.width !== width) {
                boost.canvas.width = width;
            }
            if (boost.canvas.height !== height) {
                boost.canvas.height = height;
            }
            boost.target.attr({
                x: 0,
                y: 0,
                width: width,
                height: height,
                style: 'pointer-events: none',
                href: b64BlankPixel
            });
            if (boost.clipRect) {
                boost.clipRect.attr(getBoostClipRect(chart, target));
            }
            return ctx;
        },
        /**
         * Draw the canvas image inside an SVG image
         *
         * @private
         * @function Highcharts.Series#canvasToSVG
         */
        canvasToSVG: function () {
            if (!isChartSeriesBoosting(this.chart)) {
                if (this.boost && this.boost.copy) {
                    this.boost.copy();
                }
                else if (this.chart.boost && this.chart.boost.copy) {
                    this.chart.boost.copy();
                }
            }
            else if (this.boost && this.boost.clear) {
                this.boost.clear();
            }
        },
        cvsLineTo: function (ctx, clientX, plotY) {
            ctx.lineTo(clientX, plotY);
        },
        renderCanvas: function () {
            let series = this, options = series.options, chart = series.chart, xAxis = this.xAxis, yAxis = this.yAxis, activeBoostSettings = chart.options.boost || {}, boostSettings = {
                timeRendering: activeBoostSettings.timeRendering || false,
                timeSeriesProcessing: activeBoostSettings.timeSeriesProcessing || false,
                timeSetup: activeBoostSettings.timeSetup || false
            }, ctx, c = 0, xData = series.processedXData, yData = series.processedYData, rawData = options.data, xExtremes = xAxis.getExtremes(), xMin = xExtremes.min, xMax = xExtremes.max, yExtremes = yAxis.getExtremes(), yMin = yExtremes.min, yMax = yExtremes.max, pointTaken = {}, lastClientX, sampling = !!series.sampling, points, r = options.marker && options.marker.radius, cvsDrawPoint = this.cvsDrawPoint, cvsLineTo = options.lineWidth ? this.cvsLineTo : void 0, cvsMarker = (r && r <= 1 ?
                this.cvsMarkerSquare :
                this.cvsMarkerCircle), strokeBatch = this.cvsStrokeBatch || 1000, enableMouseTracking = options.enableMouseTracking !== false, lastPoint, threshold = options.threshold, yBottom = yAxis.getThreshold(threshold), hasThreshold = isNumber(threshold), translatedThreshold = yBottom, doFill = this.fill, isRange = (series.pointArrayMap &&
                series.pointArrayMap.join(',') === 'low,high'), isStacked = !!options.stacking, cropStart = series.cropStart || 0, loadingOptions = chart.options.loading, requireSorting = series.requireSorting, wasNull, connectNulls = options.connectNulls, useRaw = !xData, minVal, maxVal, minI, maxI, index, sdata = (isStacked ?
                series.data :
                (xData || rawData)), fillColor = (series.fillOpacity ?
                Color.parse(series.color).setOpacity(pick(options.fillOpacity, 0.75)).get() :
                series.color), 
            //
            stroke = function () {
                if (doFill) {
                    ctx.fillStyle = fillColor;
                    ctx.fill();
                }
                else {
                    ctx.strokeStyle = series.color;
                    ctx.lineWidth = options.lineWidth;
                    ctx.stroke();
                }
            }, 
            //
            drawPoint = function (clientX, plotY, yBottom, i) {
                if (c === 0) {
                    ctx.beginPath();
                    if (cvsLineTo) {
                        ctx.lineJoin = 'round';
                    }
                }
                if (chart.scroller &&
                    series.options.className ===
                        'highcharts-navigator-series') {
                    plotY += chart.scroller.top;
                    if (yBottom) {
                        yBottom += chart.scroller.top;
                    }
                }
                else {
                    plotY += chart.plotTop;
                }
                clientX += chart.plotLeft;
                if (wasNull) {
                    ctx.moveTo(clientX, plotY);
                }
                else {
                    if (cvsDrawPoint) {
                        cvsDrawPoint(ctx, clientX, plotY, yBottom, lastPoint);
                    }
                    else if (cvsLineTo) {
                        cvsLineTo(ctx, clientX, plotY);
                    }
                    else if (cvsMarker) {
                        cvsMarker.call(series, ctx, clientX, plotY, r, i);
                    }
                }
                // We need to stroke the line for every 1000 pixels. It will
                // crash the browser memory use if we stroke too
                // infrequently.
                c = c + 1;
                if (c === strokeBatch) {
                    stroke();
                    c = 0;
                }
                // Area charts need to keep track of the last point
                lastPoint = {
                    clientX: clientX,
                    plotY: plotY,
                    yBottom: yBottom
                };
            }, 
            //
            compareX = options.findNearestPointBy === 'x', 
            //
            xDataFull = (this.xData ||
                this.options.xData ||
                this.processedXData ||
                false), 
            //
            addKDPoint = function (clientX, plotY, i) {
                // Shaves off about 60ms compared to repeated concatenation
                index = compareX ? clientX : clientX + ',' + plotY;
                // The k-d tree requires series points.
                // Reduce the amount of points, since the time to build the
                // tree increases exponentially.
                if (enableMouseTracking && !pointTaken[index]) {
                    pointTaken[index] = true;
                    if (chart.inverted) {
                        clientX = xAxis.len - clientX;
                        plotY = yAxis.len - plotY;
                    }
                    points.push({
                        x: xDataFull ?
                            xDataFull[cropStart + i] :
                            false,
                        clientX: clientX,
                        plotX: clientX,
                        plotY: plotY,
                        i: cropStart + i
                    });
                }
            }, boost = this.boost || {};
            if (boost.target) {
                boost.target.attr({ href: b64BlankPixel });
            }
            // If we are zooming out from SVG mode, destroy the graphics
            if (this.points || this.graph) {
                destroyGraphics(this);
            }
            // The group
            series.plotGroup('group', 'series', series.visible ? 'visible' : 'hidden', options.zIndex, chart.seriesGroup);
            series.markerGroup = series.group;
            addEvent(series, 'destroy', function () {
                // Prevent destroy twice
                series.markerGroup = null;
            });
            points = this.points = [];
            ctx = this.getContext();
            series.buildKDTree = noop; // Do not start building while drawing
            if (boost.clear) {
                boost.clear();
            }
            // if (this.canvas) {
            //     ctx.clearRect(
            //         0,
            //         0,
            //         this.canvas.width,
            //         this.canvas.height
            //     );
            // }
            if (!this.visible) {
                return;
            }
            // Display a loading indicator
            if (rawData.length > 99999) {
                chart.options.loading = merge(loadingOptions, {
                    labelStyle: {
                        backgroundColor: color("#ffffff" /* Palette.backgroundColor */).setOpacity(0.75).get(),
                        padding: '1em',
                        borderRadius: '0.5em'
                    },
                    style: {
                        backgroundColor: 'none',
                        opacity: 1
                    }
                });
                U.clearTimeout(destroyLoadingDiv);
                chart.showLoading('Drawing...');
                chart.options.loading = loadingOptions; // reset
            }
            if (boostSettings.timeRendering) {
                console.time('canvas rendering'); // eslint-disable-line no-console
            }
            // Loop over the points
            H.eachAsync(sdata, function (d, i) {
                let x, y, clientX, plotY, isNull, low, isNextInside = false, isPrevInside = false, nx = false, px = false, chartDestroyed = typeof chart.index === 'undefined', isYInside = true;
                if (!chartDestroyed) {
                    if (useRaw) {
                        x = d[0];
                        y = d[1];
                        if (sdata[i + 1]) {
                            nx = sdata[i + 1][0];
                        }
                        if (sdata[i - 1]) {
                            px = sdata[i - 1][0];
                        }
                    }
                    else {
                        x = d;
                        y = yData[i];
                        if (sdata[i + 1]) {
                            nx = sdata[i + 1];
                        }
                        if (sdata[i - 1]) {
                            px = sdata[i - 1];
                        }
                    }
                    if (nx && nx >= xMin && nx <= xMax) {
                        isNextInside = true;
                    }
                    if (px && px >= xMin && px <= xMax) {
                        isPrevInside = true;
                    }
                    // Resolve low and high for range series
                    if (isRange) {
                        if (useRaw) {
                            y = d.slice(1, 3);
                        }
                        low = y[0];
                        y = y[1];
                    }
                    else if (isStacked) {
                        x = d.x;
                        y = d.stackY;
                        low = y - d.y;
                    }
                    isNull = y === null;
                    // Optimize for scatter zooming
                    if (!requireSorting) {
                        isYInside = y >= yMin && y <= yMax;
                    }
                    if (!isNull &&
                        ((x >= xMin && x <= xMax && isYInside) ||
                            (isNextInside || isPrevInside))) {
                        clientX = Math.round(xAxis.toPixels(x, true));
                        if (sampling) {
                            if (typeof minI === 'undefined' ||
                                clientX === lastClientX) {
                                if (!isRange) {
                                    low = y;
                                }
                                if (typeof maxI === 'undefined' || y > maxVal) {
                                    maxVal = y;
                                    maxI = i;
                                }
                                if (typeof minI === 'undefined' ||
                                    low < minVal) {
                                    minVal = low;
                                    minI = i;
                                }
                            }
                            // Add points and reset
                            if (clientX !== lastClientX) {
                                // maxI also a number:
                                if (typeof minI !== 'undefined') {
                                    plotY = yAxis.toPixels(maxVal, true);
                                    yBottom = yAxis.toPixels(minVal, true);
                                    drawPoint(clientX, hasThreshold ?
                                        Math.min(plotY, translatedThreshold) : plotY, hasThreshold ?
                                        Math.max(yBottom, translatedThreshold) : yBottom, i);
                                    addKDPoint(clientX, plotY, maxI);
                                    if (yBottom !== plotY) {
                                        addKDPoint(clientX, yBottom, minI);
                                    }
                                }
                                minI = maxI = void 0;
                                lastClientX = clientX;
                            }
                        }
                        else {
                            plotY = Math.round(yAxis.toPixels(y, true));
                            drawPoint(clientX, plotY, yBottom, i);
                            addKDPoint(clientX, plotY, i);
                        }
                    }
                    wasNull = isNull && !connectNulls;
                    if (i % CHUNK_SIZE === 0) {
                        if (series.boost &&
                            series.boost.copy) {
                            series.boost.copy();
                        }
                        else if (series.chart.boost &&
                            series.chart.boost.copy) {
                            series.chart.boost.copy();
                        }
                    }
                }
                return !chartDestroyed;
            }, function () {
                const loadingDiv = chart.loadingDiv, loadingShown = chart.loadingShown;
                stroke();
                // if (series.boostCopy || series.chart.boostCopy) {
                //     (series.boostCopy || series.chart.boostCopy)();
                // }
                series.canvasToSVG();
                if (boostSettings.timeRendering) {
                    console.timeEnd('canvas rendering'); // eslint-disable-line no-console
                }
                fireEvent(series, 'renderedCanvas');
                // Do not use chart.hideLoading, as it runs JS animation and
                // will be blocked by buildKDTree. CSS animation looks good, but
                // then it must be deleted in timeout. If we add the module to
                // core, change hideLoading so we can skip this block.
                if (loadingShown) {
                    extend(loadingDiv.style, {
                        transition: 'opacity 250ms',
                        opacity: 0
                    });
                    chart.loadingShown = false;
                    destroyLoadingDiv = setTimeout(function () {
                        if (loadingDiv.parentNode) { // In exporting it is falsy
                            loadingDiv.parentNode.removeChild(loadingDiv);
                        }
                        chart.loadingDiv = chart.loadingSpan = null;
                    }, 250);
                }
                // Go back to prototype, ready to build
                delete series.buildKDTree;
                series.buildKDTree();
                // Don't do async on export, the exportChart, getSVGForExport and
                // getSVG methods are not chained for it.
            }, chart.renderer.forExport ? Number.MAX_VALUE : void 0);
        }
    });
    seriesTypes.scatter.prototype.cvsMarkerCircle = function (ctx, clientX, plotY, r) {
        ctx.moveTo(clientX, plotY);
        ctx.arc(clientX, plotY, r, 0, 2 * Math.PI, false);
    };
    // Rect is twice as fast as arc, should be used for small markers
    seriesTypes.scatter.prototype.cvsMarkerSquare = function (ctx, clientX, plotY, r) {
        ctx.rect(clientX - r, plotY - r, r * 2, r * 2);
    };
    seriesTypes.scatter.prototype.fill = true;
    if (seriesTypes.bubble) {
        seriesTypes.bubble.prototype.cvsMarkerCircle = function (ctx, clientX, plotY, r, i) {
            ctx.moveTo(clientX, plotY);
            ctx.arc(clientX, plotY, this.radii && this.radii[i], 0, 2 * Math.PI, false);
        };
        seriesTypes.bubble.prototype.cvsStrokeBatch = 1;
    }
    extend(seriesTypes.area.prototype, {
        cvsDrawPoint: function (ctx, clientX, plotY, yBottom, lastPoint) {
            if (lastPoint && clientX !== lastPoint.clientX) {
                ctx.moveTo(lastPoint.clientX, lastPoint.yBottom);
                ctx.lineTo(lastPoint.clientX, lastPoint.plotY);
                ctx.lineTo(clientX, plotY);
                ctx.lineTo(clientX, yBottom);
            }
        },
        fill: true,
        fillOpacity: true,
        sampling: true
    });
    extend(seriesTypes.column.prototype, {
        cvsDrawPoint: function (ctx, clientX, plotY, yBottom) {
            ctx.rect(clientX - 1, plotY, 1, yBottom - plotY);
        },
        fill: true,
        sampling: true
    });
    Chart.prototype.callbacks.push(function (chart) {
        /**
         * @private
         */
        function canvasToSVG() {
            if (chart.boost && chart.boost.copy) {
                chart.boost.copy();
            }
        }
        /**
         * @private
         */
        function clear() {
            const boost = this.boost || {};
            if (boost.target) {
                boost.target.attr({ href: b64BlankPixel });
            }
            if (boost.canvas) {
                boost.canvas.getContext('2d').clearRect(0, 0, boost.canvas.width, boost.canvas.height);
            }
        }
        addEvent(chart, 'predraw', clear);
        addEvent(chart, 'render', canvasToSVG);
    });
};
export default initCanvasBoost;
