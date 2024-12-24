Highcharts.setOptions({
	colors: ['var(--dark-color)', 'var(--light-color)', 'var(--normal-color)'],
	lang: {
		contextButtonTitle: 'Print and download',
		printChart: 'Print',
		downloadPNG: 'Download as image',
		downloadPDF: 'Download as PDF',
		downloadSVG: 'Download as SVG',
		downloadCSV: 'Download as CSV',
		downloadXLS: 'Download as Excel',
		loading: 'Loading...',
		shortMonths: [
			'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
		]
	},
	exporting: {
		sourceWidth: 1500,
		sourceHeight: 600,
		buttons: {
			contextButton: {
				menuItems: ["printChart", "separator", "downloadPNG", "downloadPDF", "downloadSVG", "separator", "downloadCSV", "downloadXLS"]
			}
		}
	}
});