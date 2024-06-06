module.exports = {
    mode: 'development',
    entry: './src/index.js',
    output: {
      filename: './dist/app.js',
      path: __dirname
    },
    
    resolve: {
      fallback: {
        path: require.resolve('path-browserify'),
        process: require.resolve('process-js'),
      }
    }
  };