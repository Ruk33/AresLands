// Karma configuration
// Generated on Wed Jul 10 2013 18:24:23 GMT-0300 (Hora estándar de Argentina)


// base path, that will be used to resolve files and exclude
basePath = '../';


// list of files / patterns to load in the browser
files = [
  JASMINE,
  JASMINE_ADAPTER,

  'vendor/angular.min.js',
  'vendor/angular-resource.js',
  'vendor/angular-loader.js',
  'vendor/angular-mocks.js',
  'vendor/angular-sanitize.js',
  //'vendor/angular-scenario.js',

  'configuration.js',
  'app.js',
  'services.js',
  'controllers.js',
  'filters.js',

  'tests/unit/*'
];


// list of files to exclude
exclude = [
  
];


// test results reporter to use
// possible values: 'dots', 'progress', 'junit'
reporters = ['progress'];


// web server port
port = 9876;


// cli runner port
runnerPort = 9100;


// enable / disable colors in the output (reporters and logs)
colors = true;


// level of logging
// possible values: LOG_DISABLE || LOG_ERROR || LOG_WARN || LOG_INFO || LOG_DEBUG
logLevel = LOG_INFO;


// enable / disable watching file and executing tests whenever any file changes
autoWatch = true;


// Start these browsers, currently available:
// - Chrome
// - ChromeCanary
// - Firefox
// - Opera
// - Safari (only Mac)
// - PhantomJS
// - IE (only Windows)
browsers = ['Chrome'];


// If browser does not capture in given timeout [ms], kill it
captureTimeout = 60000;


// Continuous Integration mode
// if true, it capture browsers, run tests and exit
singleRun = false;
