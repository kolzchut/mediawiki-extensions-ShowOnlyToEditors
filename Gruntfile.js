/* eslint-env node, es6 */
module.exports = function ( grunt ) {
	var conf = grunt.file.readJSON( 'extension.json' );

	grunt.loadNpmTasks( 'grunt-banana-checker' );

	grunt.initConfig( {
		banana: conf.MessagesDirs
	} );

	grunt.registerTask( 'test', [ 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
