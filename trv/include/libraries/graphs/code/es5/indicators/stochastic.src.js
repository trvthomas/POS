/**
 * @license Highstock JS v11.0.0 (2023-04-26)
 *
 * Indicator series type for Highcharts Stock
 *
 * (c) 2010-2021 Paweł Fus
 *
 * License: www.highcharts.com/license
 */
(function (factory) {
    if (typeof module === 'object' && module.exports) {
        factory['default'] = factory;
        module.exports = factory;
    } else if (typeof define === 'function' && define.amd) {
        define('highcharts/indicators/stochastic', ['highcharts', 'highcharts/modules/stock'], function (Highcharts) {
            factory(Highcharts);
            factory.Highcharts = Highcharts;
            return factory;
        });
    } else {
        factory(typeof Highcharts !== 'undefined' ? Highcharts : undefined);
    }
}(function (Highcharts) {
    'use strict';
    var _modules = Highcharts ? Highcharts._modules : {};
    function _registerModule(obj, path, args, fn) {
        if (!obj.hasOwnProperty(path)) {
            obj[path] = fn.apply(null, args);

            if (typeof CustomEvent === 'function') {
                window.dispatchEvent(
                    new CustomEvent(
                        'HighchartsModuleLoaded',
                        { detail: { path: path, module: obj[path] }
                    })
                );
            }
        }
    }
    _registerModule(_modules, 'Stock/Indicators/ArrayUtilities.js', [], function () {
        /**
         *
         *  (c) 2010-2021 Pawel Fus & Daniel Studencki
         *
         *  License: www.highcharts.com/license
         *
         *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
         *
         * */
        /* *
         *
         *  Functions
         *
         * */
        /**
         * Get extremes of array filled by OHLC data.
         *
         * @private
         *
         * @param {Array<Array<number>>} arr
         * Array of OHLC points (arrays).
         *
         * @param {number} minIndex
         * Index of "low" value in point array.
         *
         * @param {number} maxIndex
         * Index of "high" value in point array.
         *
         * @return {Array<number,number>}
         * Returns array with min and max value.
         */
        function getArrayExtremes(arr, minIndex, maxIndex) {
            return arr.reduce(function (prev, target) { return [
                Math.min(prev[0], target[minIndex]),
                Math.max(prev[1], target[maxIndex])
            ]; }, [Number.MAX_VALUE, -Number.MAX_VALUE]);
        }
        /* *
         *
         *  Default Export
         *
         * */
        var ArrayUtilities = {
            getArrayExtremes: getArrayExtremes
        };

        return ArrayUtilities;
    });
    _registerModule(_modules, 'Stock/Indicators/MultipleLinesComposition.js', [_modules['Core/Series/SeriesRegistry.js'], _modules['Core/Utilities.js']], function (SeriesRegistry, U) {
        /**
         *
         *  (c) 2010-2021 Wojciech Chmiel
         *
         *  License: www.highcharts.com/license
         *
         *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
         *
         * */
        var smaProto = SeriesRegistry.seriesTypes.sma.prototype;
        var defined = U.defined, error = U.error, merge = U.merge;
        /* *
         *
         *  Composition
         *
         * */
        var MultipleLinesComposition;
        (function (MultipleLinesComposition) {
            /* *
             *
             *  Declarations
             *
             * */
            /* *
            *
            *  Constants
            *
            * */
            var composedMembers = [];
            /**
             * Additional lines DOCS names. Elements of linesApiNames array should
             * be consistent with DOCS line names defined in your implementation.
             * Notice that linesApiNames should have decreased amount of elements
             * relative to pointArrayMap (without pointValKey).
             *
             * @private
             * @type {Array<string>}
             */
            var linesApiNames = ['bottomLine'];
            /**
             * Lines ids. Required to plot appropriate amount of lines.
             * Notice that pointArrayMap should have more elements than
             * linesApiNames, because it contains main line and additional lines ids.
             * Also it should be consistent with amount of lines calculated in
             * getValues method from your implementation.
             *
             * @private
             * @type {Array<string>}
             */
            var pointArrayMap = ['top', 'bottom'];
            /**
             * Names of the lines, bewteen which the area should be plotted.
             * If the drawing of the area should
             * be disabled for some indicators, leave this option as an empty array.
             * Names should be the same as the names in the pointArrayMap.
             *
             * @private
             * @type {Array<string>}
             */
            var areaLinesNames = ['top'];
            /**
             * Main line id.
             *
             * @private
             * @type {string}
             */
            var pointValKey = 'top';
            /* *
            *
            *  Functions
            *
            * */
            /**
             * Composition useful for all indicators that have more than one line.
             * Compose it with your implementation where you will provide the
             * `getValues` method appropriate to your indicator and `pointArrayMap`,
             * `pointValKey`, `linesApiNames` properties. Notice that `pointArrayMap`
             * should be consistent with the amount of lines calculated in the
             * `getValues` method.
             *
             * @private
             */
            function compose(IndicatorClass) {
                if (U.pushUnique(composedMembers, IndicatorClass)) {
                    var proto = IndicatorClass.prototype;
                    proto.linesApiNames = (proto.linesApiNames ||
                        linesApiNames.slice());
                    proto.pointArrayMap = (proto.pointArrayMap ||
                        pointArrayMap.slice());
                    proto.pointValKey = (proto.pointValKey ||
                        pointValKey);
                    proto.areaLinesNames = (proto.areaLinesNames ||
                        areaLinesNames.slice());
                    proto.drawGraph = indicatorDrawGraph;
                    proto.getGraphPath = indicatorGetGraphPath;
                    proto.toYData = indicatorToYData;
                    proto.translate = indicatorTranslate;
                }
                return IndicatorClass;
            }
            MultipleLinesComposition.compose = compose;
            /**
             * Generate the API name of the line
             *
             * @private
             * @param propertyName name of the line
             */
            function getLineName(propertyName) {
                return ('plot' +
                    propertyName.charAt(0).toUpperCase() +
                    propertyName.slice(1));
            }
            /**
             * Create translatedLines Collection based on pointArrayMap.
             *
             * @private
             * @param {string} [excludedValue]
             *        Main line id
             * @return {Array<string>}
             *         Returns translated lines names without excluded value.
             */
            function getTranslatedLinesNames(indicator, excludedValue) {
                var translatedLines = [];
                (indicator.pointArrayMap || []).forEach(function (propertyName) {
                    if (propertyName !== excludedValue) {
                        translatedLines.push(getLineName(propertyName));
                    }
                });
                return translatedLines;
            }
            /**
             * Draw main and additional lines.
             *
             * @private
             */
            function indicatorDrawGraph() {
                var indicator = this, pointValKey = indicator.pointValKey, linesApiNames = indicator.linesApiNames, areaLinesNames = indicator.areaLinesNames, mainLinePoints = indicator.points, mainLineOptions = indicator.options, mainLinePath = indicator.graph, gappedExtend = {
                    options: {
                        gapSize: mainLineOptions.gapSize
                    }
                }, 
                // additional lines point place holders:
                secondaryLines = [], secondaryLinesNames = getTranslatedLinesNames(indicator, pointValKey);
                var pointsLength = mainLinePoints.length, point;
                // Generate points for additional lines:
                secondaryLinesNames.forEach(function (plotLine, index) {
                    // create additional lines point place holders
                    secondaryLines[index] = [];
                    while (pointsLength--) {
                        point = mainLinePoints[pointsLength];
                        secondaryLines[index].push({
                            x: point.x,
                            plotX: point.plotX,
                            plotY: point[plotLine],
                            isNull: !defined(point[plotLine])
                        });
                    }
                    pointsLength = mainLinePoints.length;
                });
                // Modify options and generate area fill:
                if (indicator.userOptions.fillColor && areaLinesNames.length) {
                    var index = secondaryLinesNames.indexOf(getLineName(areaLinesNames[0])), secondLinePoints = secondaryLines[index], firstLinePoints = areaLinesNames.length === 1 ?
                        mainLinePoints :
                        secondaryLines[secondaryLinesNames.indexOf(getLineName(areaLinesNames[1]))], originalColor = indicator.color;
                    indicator.points = firstLinePoints;
                    indicator.nextPoints = secondLinePoints;
                    indicator.color = indicator.userOptions.fillColor;
                    indicator.options = merge(mainLinePoints, gappedExtend);
                    indicator.graph = indicator.area;
                    indicator.fillGraph = true;
                    smaProto.drawGraph.call(indicator);
                    indicator.area = indicator.graph;
                    // Clean temporary properties:
                    delete indicator.nextPoints;
                    delete indicator.fillGraph;
                    indicator.color = originalColor;
                }
                // Modify options and generate additional lines:
                linesApiNames.forEach(function (lineName, i) {
                    if (secondaryLines[i]) {
                        indicator.points = secondaryLines[i];
                        if (mainLineOptions[lineName]) {
                            indicator.options = merge(mainLineOptions[lineName].styles, gappedExtend);
                        }
                        else {
                            error('Error: "There is no ' + lineName +
                                ' in DOCS options declared. Check if linesApiNames' +
                                ' are consistent with your DOCS line names."');
                        }
                        indicator.graph = indicator['graph' + lineName];
                        smaProto.drawGraph.call(indicator);
                        // Now save lines:
                        indicator['graph' + lineName] = indicator.graph;
                    }
                    else {
                        error('Error: "' + lineName + ' doesn\'t have equivalent ' +
                            'in pointArrayMap. To many elements in linesApiNames ' +
                            'relative to pointArrayMap."');
                    }
                });
                // Restore options and draw a main line:
                indicator.points = mainLinePoints;
                indicator.options = mainLineOptions;
                indicator.graph = mainLinePath;
                smaProto.drawGraph.call(indicator);
            }
            /**
             * Create the path based on points provided as argument.
             * If indicator.nextPoints option is defined, create the areaFill.
             *
             * @private
             * @param points Points on which the path should be created
             */
            function indicatorGetGraphPath(points) {
                var areaPath, path = [], higherAreaPath = [];
                points = points || this.points;
                // Render Span
                if (this.fillGraph && this.nextPoints) {
                    areaPath = smaProto.getGraphPath.call(this, this.nextPoints);
                    if (areaPath && areaPath.length) {
                        areaPath[0][0] = 'L';
                        path = smaProto.getGraphPath.call(this, points);
                        higherAreaPath = areaPath.slice(0, path.length);
                        // Reverse points, so that the areaFill will start from the end:
                        for (var i = higherAreaPath.length - 1; i >= 0; i--) {
                            path.push(higherAreaPath[i]);
                        }
                    }
                }
                else {
                    path = smaProto.getGraphPath.apply(this, arguments);
                }
                return path;
            }
            /**
             * @private
             * @param {Highcharts.Point} point
             *        Indicator point
             * @return {Array<number>}
             *         Returns point Y value for all lines
             */
            function indicatorToYData(point) {
                var pointColl = [];
                (this.pointArrayMap || []).forEach(function (propertyName) {
                    pointColl.push(point[propertyName]);
                });
                return pointColl;
            }
            /**
             * Add lines plot pixel values.
             *
             * @private
             */
            function indicatorTranslate() {
                var _this = this;
                var pointArrayMap = this.pointArrayMap;
                var LinesNames = [], value;
                LinesNames = getTranslatedLinesNames(this);
                smaProto.translate.apply(this, arguments);
                this.points.forEach(function (point) {
                    pointArrayMap.forEach(function (propertyName, i) {
                        value = point[propertyName];
                        // If the modifier, like for example compare exists,
                        // modified the original value by that method, #15867.
                        if (_this.dataModify) {
                            value = _this.dataModify.modifyValue(value);
                        }
                        if (value !== null) {
                            point[LinesNames[i]] = _this.yAxis.toPixels(value, true);
                        }
                    });
                });
            }
        })(MultipleLinesComposition || (MultipleLinesComposition = {}));
        /* *
         *
         *  Default Export
         *
         * */

        return MultipleLinesComposition;
    });
    _registerModule(_modules, 'Stock/Indicators/Stochastic/StochasticIndicator.js', [_modules['Stock/Indicators/ArrayUtilities.js'], _modules['Stock/Indicators/MultipleLinesComposition.js'], _modules['Core/Series/SeriesRegistry.js'], _modules['Core/Utilities.js']], function (AU, MultipleLinesComposition, SeriesRegistry, U) {
        /* *
         *
         *  License: www.highcharts.com/license
         *
         *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
         *
         * */
        var __extends = (this && this.__extends) || (function () {
            var extendStatics = function (d, b) {
                extendStatics = Object.setPrototypeOf ||
                    ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
                    function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
                return extendStatics(d, b);
            };
            return function (d, b) {
                if (typeof b !== "function" && b !== null)
                    throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
                extendStatics(d, b);
                function __() { this.constructor = d; }
                d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
            };
        })();
        var SMAIndicator = SeriesRegistry.seriesTypes.sma;
        var extend = U.extend, isArray = U.isArray, merge = U.merge;
        /* *
         *
         *  Class
         *
         * */
        /**
         * The Stochastic series type.
         *
         * @private
         * @class
         * @name Highcharts.seriesTypes.stochastic
         *
         * @augments Highcharts.Series
         */
        var StochasticIndicator = /** @class */ (function (_super) {
            __extends(StochasticIndicator, _super);
            function StochasticIndicator() {
                /* *
                 *
                 *  Static Properties
                 *
                 * */
                var _this = _super !== null && _super.apply(this, arguments) || this;
                /* *
                 *
                 *  Properties
                 *
                 * */
                _this.data = void 0;
                _this.options = void 0;
                _this.points = void 0;
                return _this;
            }
            /* *
             *
             *  Functions
             *
             * */
            StochasticIndicator.prototype.init = function () {
                _super.prototype.init.apply(this, arguments);
                // Set default color for lines:
                this.options = merge({
                    smoothedLine: {
                        styles: {
                            lineColor: this.color
                        }
                    }
                }, this.options);
            };
            StochasticIndicator.prototype.getValues = function (series, params) {
                var periodK = params.periods[0], periodD = params.periods[1], xVal = series.xData, yVal = series.yData, yValLen = yVal ? yVal.length : 0, 
                // 0- date, 1-%K, 2-%D
                SO = [], xData = [], yData = [], close = 3, low = 2, high = 1;
                var slicedY, CL, HL, LL, K, D = null, points, extremes, i;
                // Stochastic requires close value
                if (yValLen < periodK ||
                    !isArray(yVal[0]) ||
                    yVal[0].length !== 4) {
                    return;
                }
                // If the value of initial points is constant, wait until it changes
                // to calculate correct Stochastic values
                var constantValues = true, j = 0;
                // For a N-period, we start from N-1 point, to calculate Nth point
                // That is why we later need to comprehend slice() elements list
                // with (+1)
                for (i = periodK - 1; i < yValLen; i++) {
                    slicedY = yVal.slice(i - periodK + 1, i + 1);
                    // Calculate %K
                    extremes = AU.getArrayExtremes(slicedY, low, high);
                    LL = extremes[0]; // Lowest low in %K periods
                    CL = yVal[i][close] - LL;
                    HL = extremes[1] - LL;
                    K = CL / HL * 100;
                    if (isNaN(K) && constantValues) {
                        j++;
                        continue;
                    }
                    else if (constantValues && !isNaN(K)) {
                        constantValues = false;
                    }
                    var length_1 = xData.push(xVal[i]);
                    // If N-period previous values are constant which results in NaN %K,
                    // we need to use previous %K value if it is a number,
                    // otherwise we should use null
                    if (isNaN(K)) {
                        yData.push([
                            yData[length_1 - 2] &&
                                typeof yData[length_1 - 2][0] === 'number' ?
                                yData[length_1 - 2][0] : null,
                            null
                        ]);
                    }
                    else {
                        yData.push([K, null]);
                    }
                    // Calculate smoothed %D, which is SMA of %K
                    if (i >= j + (periodK - 1) + (periodD - 1)) {
                        points = _super.prototype.getValues.call(this, {
                            xData: xData.slice(-periodD),
                            yData: yData.slice(-periodD)
                        }, {
                            period: periodD
                        });
                        D = points.yData[0];
                    }
                    SO.push([xVal[i], K, D]);
                    yData[length_1 - 1][1] = D;
                }
                return {
                    values: SO,
                    xData: xData,
                    yData: yData
                };
            };
            /**
             * Stochastic oscillator. This series requires the `linkedTo` option to be
             * set and should be loaded after the `stock/indicators/indicators.js` file.
             *
             * @sample stock/indicators/stochastic
             *         Stochastic oscillator
             *
             * @extends      plotOptions.sma
             * @since        6.0.0
             * @product      highstock
             * @excluding    allAreas, colorAxis, joinBy, keys, navigatorOptions,
             *               pointInterval, pointIntervalUnit, pointPlacement,
             *               pointRange, pointStart, showInNavigator, stacking
             * @requires     stock/indicators/indicators
             * @requires     stock/indicators/stochastic
             * @optionparent plotOptions.stochastic
             */
            StochasticIndicator.defaultOptions = merge(SMAIndicator.defaultOptions, {
                /**
                 * @excluding index, period
                 */
                params: {
                    // Index and period are unchangeable, do not inherit (#15362)
                    index: void 0,
                    period: void 0,
                    /**
                     * Periods for Stochastic oscillator: [%K, %D].
                     *
                     * @type    {Array<number,number>}
                     * @default [14, 3]
                     */
                    periods: [14, 3]
                },
                marker: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: '<span style="color:{point.color}">\u25CF</span><b> {series.name}</b><br/>%K: {point.y}<br/>%D: {point.smoothed}<br/>'
                },
                /**
                 * Smoothed line options.
                 */
                smoothedLine: {
                    /**
                     * Styles for a smoothed line.
                     */
                    styles: {
                        /**
                         * Pixel width of the line.
                         */
                        lineWidth: 1,
                        /**
                         * Color of the line. If not set, it's inherited from
                         * [plotOptions.stochastic.color
                         * ](#plotOptions.stochastic.color).
                         *
                         * @type {Highcharts.ColorString}
                         */
                        lineColor: void 0
                    }
                },
                dataGrouping: {
                    approximation: 'averages'
                }
            });
            return StochasticIndicator;
        }(SMAIndicator));
        extend(StochasticIndicator.prototype, {
            areaLinesNames: [],
            nameComponents: ['periods'],
            nameBase: 'Stochastic',
            pointArrayMap: ['y', 'smoothed'],
            parallelArrays: ['x', 'y', 'smoothed'],
            pointValKey: 'y',
            linesApiNames: ['smoothedLine']
        });
        MultipleLinesComposition.compose(StochasticIndicator);
        SeriesRegistry.registerSeriesType('stochastic', StochasticIndicator);
        /* *
         *
         *  Default Export
         *
         * */
        /* *
         *
         *  API Options
         *
         * */
        /**
         * A Stochastic indicator. If the [type](#series.stochastic.type) option is not
         * specified, it is inherited from [chart.type](#chart.type).
         *
         * @extends   series,plotOptions.stochastic
         * @since     6.0.0
         * @product   highstock
         * @excluding allAreas, colorAxis,  dataParser, dataURL, joinBy, keys,
         *            navigatorOptions, pointInterval, pointIntervalUnit,
         *            pointPlacement, pointRange, pointStart, showInNavigator, stacking
         * @requires  stock/indicators/indicators
         * @requires  stock/indicators/stochastic
         * @apioption series.stochastic
         */
        ''; // to include_once the above in the js output

        return StochasticIndicator;
    });
    _registerModule(_modules, 'masters/indicators/stochastic.src.js', [], function () {


    });
}));