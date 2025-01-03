/* *
 *
 *  License: www.highcharts.com/license
 *
 *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
 *
 * */
'use strict';
import MultipleLinesComposition from '../MultipleLinesComposition.js';
import SeriesRegistry from '../../../Core/Series/SeriesRegistry.js';
const { sma: SMAIndicator } = SeriesRegistry.seriesTypes;
import U from '../../../Core/Utilities.js';
const { extend, isArray, merge } = U;
/* *
 *
 *  Class
 *
 * */
/**
 * The Price Envelopes series type.
 *
 * @private
 * @class
 * @name Highcharts.seriesTypes.priceenvelopes
 *
 * @augments Highcharts.Series
 */
class PriceEnvelopesIndicator extends SMAIndicator {
    constructor() {
        /* *
         *
         *  Static Properties
         *
         * */
        super(...arguments);
        /* *
         *
         *  Properties
         *
         * */
        this.data = void 0;
        this.options = void 0;
        this.points = void 0;
    }
    /* *
     *
     *  Functions
     *
     * */
    init() {
        super.init.apply(this, arguments);
        // Set default color for lines:
        this.options = merge({
            topLine: {
                styles: {
                    lineColor: this.color
                }
            },
            bottomLine: {
                styles: {
                    lineColor: this.color
                }
            }
        }, this.options);
    }
    getValues(series, params) {
        const period = params.period, topPercent = params.topBand, botPercent = params.bottomBand, xVal = series.xData, yVal = series.yData, yValLen = yVal ? yVal.length : 0, 
        // 0- date, 1-top line, 2-middle line, 3-bottom line
        PE = [], 
        // middle line, top line and bottom line
        xData = [], yData = [];
        let ML, TL, BL, date, slicedX, slicedY, point, i;
        // Price envelopes requires close value
        if (xVal.length < period ||
            !isArray(yVal[0]) ||
            yVal[0].length !== 4) {
            return;
        }
        for (i = period; i <= yValLen; i++) {
            slicedX = xVal.slice(i - period, i);
            slicedY = yVal.slice(i - period, i);
            point = super.getValues({
                xData: slicedX,
                yData: slicedY
            }, params);
            date = point.xData[0];
            ML = point.yData[0];
            TL = ML * (1 + topPercent);
            BL = ML * (1 - botPercent);
            PE.push([date, TL, ML, BL]);
            xData.push(date);
            yData.push([TL, ML, BL]);
        }
        return {
            values: PE,
            xData: xData,
            yData: yData
        };
    }
}
/**
 * Price envelopes indicator based on [SMA](#plotOptions.sma) calculations.
 * This series requires the `linkedTo` option to be set and should be loaded
 * after the `stock/indicators/indicators.js` file.
 *
 * @sample stock/indicators/price-envelopes
 *         Price envelopes
 *
 * @extends      plotOptions.sma
 * @since        6.0.0
 * @product      highstock
 * @requires     stock/indicators/indicators
 * @requires     stock/indicators/price-envelopes
 * @optionparent plotOptions.priceenvelopes
 */
PriceEnvelopesIndicator.defaultOptions = merge(SMAIndicator.defaultOptions, {
    marker: {
        enabled: false
    },
    tooltip: {
        pointFormat: '<span style="color:{point.color}">\u25CF</span><b> {series.name}</b><br/>Top: {point.top}<br/>Middle: {point.middle}<br/>Bottom: {point.bottom}<br/>'
    },
    params: {
        period: 20,
        /**
         * Percentage above the moving average that should be displayed.
         * 0.1 means 110%. Relative to the calculated value.
         */
        topBand: 0.1,
        /**
         * Percentage below the moving average that should be displayed.
         * 0.1 means 90%. Relative to the calculated value.
         */
        bottomBand: 0.1
    },
    /**
     * Bottom line options.
     */
    bottomLine: {
        styles: {
            /**
             * Pixel width of the line.
             */
            lineWidth: 1,
            /**
             * Color of the line. If not set, it's inherited from
             * [plotOptions.priceenvelopes.color](
             * #plotOptions.priceenvelopes.color).
             *
             * @type {Highcharts.ColorString}
             */
            lineColor: void 0
        }
    },
    /**
     * Top line options.
     *
     * @extends plotOptions.priceenvelopes.bottomLine
     */
    topLine: {
        styles: {
            lineWidth: 1
        }
    },
    dataGrouping: {
        approximation: 'averages'
    }
    /**
     * Option for fill color between lines in Price Envelopes Indicator.
     *
     * @sample {highstock} stock/indicators/indicator-area-fill
     *      Background fill between lines.
     *
     * @type      {Highcharts.Color}
     * @since 11.0.0
     * @apioption plotOptions.priceenvelopes.fillColor
     *
     */
});
extend(PriceEnvelopesIndicator.prototype, {
    areaLinesNames: ['top', 'bottom'],
    linesApiNames: ['topLine', 'bottomLine'],
    nameComponents: ['period', 'topBand', 'bottomBand'],
    nameBase: 'Price envelopes',
    pointArrayMap: ['top', 'middle', 'bottom'],
    parallelArrays: ['x', 'y', 'top', 'bottom'],
    pointValKey: 'middle'
});
MultipleLinesComposition.compose(PriceEnvelopesIndicator);
SeriesRegistry.registerSeriesType('priceenvelopes', PriceEnvelopesIndicator);
/* *
 *
 *  Default Export
 *
 * */
export default PriceEnvelopesIndicator;
/* *
 *
 *  API Options
 *
 * */
/**
 * A price envelopes indicator. If the [type](#series.priceenvelopes.type)
 * option is not specified, it is inherited from [chart.type](#chart.type).
 *
 * @extends   series,plotOptions.priceenvelopes
 * @since     6.0.0
 * @excluding dataParser, dataURL
 * @product   highstock
 * @requires  stock/indicators/indicators
 * @requires  stock/indicators/price-envelopes
 * @apioption series.priceenvelopes
 */
''; // to include_once the above in the js output
