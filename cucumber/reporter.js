const reporter = require('cucumber-html-reporter');

reporter.generate({
   theme: 'bootstrap',
   jsonFile: 'var/report.json',
   output: 'var/report.html',
   scenarioTimestamp: true,
   reportSuiteAsScenarios: true,
   launchReport: false,
   storeScreenshots: true,
   noInlineScreenshots: true,
   screenshotsDirectory: 'var/screenshots',
});