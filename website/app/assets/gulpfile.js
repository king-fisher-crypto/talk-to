/**
 *
 * @version 20200423
 * @author samy.sadi.contact@gmail.com
 */

/*
 * Default Configuration.
 * Override this in package.json, under "CONFIG" key.
 * #####################################################################
 * */
const DEFAULT_CONFIG = {
	production: false,

	cssAutoPrefixOptions: {overrideBrowserslist: ['> 0%']},
	cssBeautify: false,
	cssBeautifyDev: true,
	cssCompatibility: '*', // could be ie7, ie8, ie9 or *
	cssLint: false,

	lessPaths: [],
	sassPaths: [],

	jsBeautify: false,
	jsBeautifyDev: false,
	jsLint: false,
	jsLintEnvs: ['browser'],
	jsLintEs6: false,
	jsLintGlobals: ['jQuery', '$'],
	jsLintOptions: {},
	jsOptimization: true,
	jsOptimizationConfig: {
		compilation_level: 'SIMPLE',
		warning_level: 'DEFAULT',
		language_in: 'ECMASCRIPT6',
		language_out: 'ECMASCRIPT5_STRICT'
	},
	jsOptimizationOnTs: true,
	jsUglify: true,

	sourceDirectories: [],
	distDirectories: [],
	directories: {
		css: 'css/',
		fonts: 'fonts/',
		images: 'images/',
		js: 'js/',
		jsLibs: 'lib/', // contains js libs in sub-folders, each subfolder is concatenated and put in a single file
		tsLibs: 'ts/',
		vendor: 'vendor/' // vendor directory is copied as is, without modifications
	},

	cssExtensions: ['css'],
	fontsExtensions: ['afm', 'bdf', 'eot', 'gsf', 'otf', 'pcf', 'pfa', 'pfb', 'pfm', 'pfr', 'psf', 'snf', 'svg', 'ttc', 'ttf', 'woff', 'woff2'],
	imagesExtensions: ['apng', 'gif', 'ico', 'jpeg', 'jpg', 'jpe', 'png', 'svg', 'tif', 'tiff', 'webp', 'xbm'],
	jsExtensions: ['js'],
	lessExtensions: ['less'],
	sassExtensions: ['scss', 'sass'],
	tsExtensions: ['ts', 'tsx'],
	vendorExtensions: false, // will match everything (except dot starting files)

	cssSuffix: false,
	fontsSuffix: false,
	imagesSuffix: false,
	jsSuffix: false,

	versioning: false,
	cssVersioning: null,
	fontsVersioning: false,
	imagesVersioning: false,
	jsVersioning: null,

	versioningManifestName: 'rev-manifest.json',
	versioningOutputs: [],
	versioningTemplates: [],
};

/*
 * Global Variables
 * #####################################################################
 */
let closureCompiler = require('google-closure-compiler').gulp();
let fs = require('fs');
let fs_rp = require('fs.realpath');
let gulp = require('gulp');
let mergeStream = require('merge-stream');
let path = require('path');
let plugins = require('gulp-load-plugins')();
let process = require('process');

let CONFIG = require('./package.json').CONFIG || {};
for (let k in DEFAULT_CONFIG) {
	if (typeof CONFIG[k] === 'undefined') {
		CONFIG[k] = DEFAULT_CONFIG[k];
	}
}

// versioning overrides other values if true
if (CONFIG.cssVersioning === null) {
	CONFIG.cssVersioning = CONFIG.versioning;
}
if (CONFIG.fontsVersioning === null) {
	CONFIG.fontsVersioning = CONFIG.versioning;
}
if (CONFIG.imagesVersioning === null) {
	CONFIG.imagesVersioning = CONFIG.versioning;
}
if (CONFIG.jsVersioning === null) {
	CONFIG.jsVersioning = CONFIG.versioning;
}

// some checks
if (CONFIG.versioningTemplates.length != CONFIG.versioningOutputs.length) {
	throw 'There must be the same number of elements in versioningTemplates and versioningOutputs';
}

/*
 * Functions
 * #####################################################################
 */

/**
 * Returns selectors matching all given extensions in the given directories.
 *
 * @param string|string[] Directory or directories.
 * @param string[] Extensions.
 * @param string Wilcard selector, defaults to '*'.
 *
 * @return string[] An array containing all selectors to match the given
 *                  extensions in the given directory or directories.
 */
let getExtensionsSelector = function(directories, extensions, wildcard) {
	if (typeof directories === 'string') {
		directories = [directories];
	}
	if (typeof extensions === 'undefined') {
		extensions = '';
	}
	if (typeof wildcard === 'undefined') {
		wildcard = '*';
	}
	let r = [];
	for (let i in directories) {
		if (extensions === false) {
			r.push(directories[i] + wildcard);
		} else {
			if (!extensions) {
				r.push(directories[i] + wildcard);
			} else {
				for (let k in extensions) {
					r.push(directories[i] + wildcard + '.' + extensions[k]);
				}
			}
		}
	}
	return r;
};

let getCssSelector = function(directory) {
	return getExtensionsSelector(directory + '**/', CONFIG.cssExtensions);
};

let getFontsSelector = function(directory) {
	return getExtensionsSelector(directory + '**/', CONFIG.fontsExtensions);
};

let getImagesSelector = function(directory) {
	return getExtensionsSelector(directory + '**/', CONFIG.imagesExtensions);
};

let getJsSelector = function(directory) {
	return getExtensionsSelector(directory + '**/', CONFIG.jsExtensions);
};

let getJsLibsSelector = function(directory) {
	return [directory + 'index.js']
		.concat(getExtensionsSelector(directory, CONFIG.jsExtensions))
		.concat([directory + '**/index.js'])
		.concat(getExtensionsSelector(directory + '**/', CONFIG.jsExtensions))
	;
};

let getLessSelector = function(directory, includeAll) {
	if (typeof includeAll === 'undefined') {
		includeAll = false;
	}
	if (includeAll) {
		return getExtensionsSelector(directory + '**/', CONFIG.lessExtensions);
	} else {
		return getExtensionsSelector(directory + '**/', CONFIG.lessExtensions, '[^_]*');
	}
};

let getSassSelector = function(directory, includeAll) {
	if (typeof includeAll === 'undefined')
		includeAll = false;
	if (includeAll) {
		return getExtensionsSelector(directory + '**/', CONFIG.sassExtensions);
	} else {
		return getExtensionsSelector(directory + '**/', CONFIG.sassExtensions, '[^_]*');
	}
};

let getTsLibsSelector = function(directory) {
	return getExtensionsSelector(directory + '**/', CONFIG.tsExtensions);
};

let getVendorSelector = function(directory) {
	return getExtensionsSelector(directory + '**/', CONFIG.vendorExtensions);
};

let getSubDirectories = function(directory) {
	try {
		return fs
			.readdirSync(directory)
			.filter(function(file) {
				return fs.statSync(path.join(directory, file)).isDirectory();
			})
		;
	} catch (e) {
		return [];
	}
};

let getTaskNames = function(tasks, pref) {
	if (!(tasks instanceof Array)) {
		return [pref + '0'];
	}
	let a = [];
	for (let i = 0; i < tasks.length; i++) {
		a.push(pref + i);
	}
	return a;
};

let registerTask = function(taskName, task) {
	if (task instanceof Array) {
		gulp.task(taskName, gulp.parallel(getTaskNames(task, taskName)));
	} else {
		gulp.task(taskName, task);
	}
	return task;
};

let runApplyRevTask = function(i, j) {
	let src = path.resolve(__dirname, CONFIG.versioningTemplates[j]);
	let out = path.resolve(__dirname, CONFIG.versioningOutputs[j]);
	let pipe = gulp.src(src);
	pipe = pipe.pipe(plugins.revReplace({
		manifest: gulp.src(path.resolve(CONFIG.distDirectories[i], CONFIG.versioningManifestName))
	}));
	pipe = pipe.pipe(plugins.rename(path.basename(out)));
	pipe = pipe.pipe(gulp.dest(path.resolve(__dirname, path.dirname(out))));
	return pipe;
};

let genApplyRev = function(i) {
	return function(done) {
		let tasks = [];
		for (let j = 0; j < CONFIG.versioningTemplates.length; ++j) {
			tasks.push(runApplyRevTask(i, j));
		}
		if (!tasks.length) {
			done();
		} else {
			return mergeStream(tasks);
		}
	};
};

let runRevManifest = function(i, pipe) {
	let p = path.resolve(CONFIG.distDirectories[i], CONFIG.versioningManifestName);
	let b = path.resolve(__dirname, CONFIG.distDirectories[i]);
	pipe = pipe.pipe(plugins.rev.manifest(p, {
		base: b,
		merge: true
	}));
	pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
	return pipe;
};

let processLess = function(pipe) {
	//
	pipe = pipe.pipe(plugins.plumber());

	//
	pipe = pipe.pipe(plugins.less({
		paths: CONFIG.lessPaths
	}));

	// rename: change ext
	pipe = pipe.pipe(plugins.rename({extname: '.css'}));

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return pipe;
};

let genLessTask = function(i, pipe) {
	return function() {
		pipe = pipe || gulp.src(getLessSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.css), {base: CONFIG.sourceDirectories[i]});
		if (!CONFIG.production) {
			pipe = pipe.pipe(plugins.sourcemaps.init());
		}
		pipe = processLess(pipe);
		pipe = lintCss(pipe);
		pipe = processCss(pipe);
		if (!CONFIG.production) {
			pipe = pipe.pipe(plugins.sourcemaps.write());
		}
		pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
		pipe = runRevManifest(i, pipe);
		return pipe;
	};
};

let processSass = function(pipe) {
	//
	pipe = pipe.pipe(plugins.plumber());

	//
	pipe = pipe.pipe(plugins.sass({includePaths: CONFIG.sassPaths}).on('error', plugins.sass.logError));

	// rename: change ext
	pipe = pipe.pipe(plugins.rename({extname: '.css'}));

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return pipe;
};

let genSassTask = function(i, pipe) {
	return function() {
		pipe = gulp.src(getSassSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.css), {base: CONFIG.sourceDirectories[i]});
		if (!CONFIG.production) {
			pipe = pipe.pipe(plugins.sourcemaps.init());
		}
		pipe = processSass(pipe);
		pipe = lintCss(pipe);
		pipe = processCss(pipe);
		if (!CONFIG.production) {
			pipe = pipe.pipe(plugins.sourcemaps.write());
		}
		pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
		pipe = runRevManifest(i, pipe);
		return pipe;
	};
};

let lintCss = function(pipe) {
	if (!CONFIG.cssLint) {
		return pipe;
	}

	//
	pipe = pipe.pipe(plugins.plumber());

	// check for errors
	pipe = pipe
		.pipe(plugins.stylelint({
			configFile: 'stylelint.js',
			failAfterError: false,
			reporters: [
				{formatter: 'string', console: true}
			]
		}))
	;

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return pipe;
};

let processCss = function(pipe) {
	//
	pipe = pipe.pipe(plugins.plumber());

	// add -webkit-, -moz-, -o- and other prefixes
	if (CONFIG.cssAutoPrefixOptions) {
		pipe = pipe.pipe(plugins.autoprefixer(CONFIG.cssAutoPrefixOptions));
	} else {
		pipe = pipe.pipe(plugins.autoprefixer());
	}

	if (CONFIG.production) {
		// minify css through csso then clean-css
		pipe = pipe
			.pipe(plugins.csso())
			.pipe(plugins.cleanCss({
				compatibility: CONFIG.cssCompatibility,
				level: 1,
				rebase: false
			}))
		;
	}

	// beautify css if necessary
	if ((CONFIG.production && CONFIG.cssBeautify) || (!CONFIG.production && CONFIG.cssBeautifyDev)) {
		pipe = pipe
			.pipe(plugins.cssbeautify({
				openbrace: 'end-of-line',
				autosemicolon: true
			}))
			.pipe(plugins.csscomb())
		;
	}

	// rename: add suffix
	if (CONFIG.cssSuffix) {
		pipe = pipe.pipe(plugins.rename({suffix: CONFIG.cssSuffix}));
	}

	// versioning
	if (CONFIG.cssVersioning) {
		pipe = pipe.pipe(plugins.rev());
	}

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return pipe;
};

let genCssTask = function(i, pipe) {
	return function() {
		pipe = pipe || gulp.src(getCssSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.css), {base: CONFIG.sourceDirectories[i]});
		if (!CONFIG.production) {
			pipe = pipe.pipe(plugins.sourcemaps.init());
		}
		pipe = lintCss(pipe);
		pipe = processCss(pipe);
		if (!CONFIG.production) {
			pipe = pipe.pipe(plugins.sourcemaps.write());
		}
		pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
		pipe = runRevManifest(i, pipe);
		return pipe;
	};
};

let processFonts = function(pipe) {
	//
	pipe = pipe.pipe(plugins.plumber());

	if (CONFIG.fontsSuffix) {
		pipe = pipe.pipe(plugins.rename({suffix: CONFIG.fontsSuffix}));
	}

	// versioning
	if (CONFIG.fontsVersioning) {
		pipe = pipe.pipe(plugins.rev());
	}

	//
	pipe = pipe.pipe(plugins.plumber.stop());
	return pipe;
};

let genFontsTask = function(i, pipe) {
	return function() {
		pipe = pipe || gulp.src(getFontsSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.fonts), {base: CONFIG.sourceDirectories[i]});
		pipe = processFonts(pipe);
		pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
		pipe = runRevManifest(i, pipe);
		return pipe;
	};
};

let processImages = function(pipe) {
	//
	pipe = pipe.pipe(plugins.plumber());

	// minify images
	pipe = pipe.pipe(plugins.imagemin([
		plugins.imagemin.gifsicle({interlaced: true, optimizationLevel: 3}),
		plugins.imagemin.mozjpeg({progressive: true}),
		plugins.imagemin.optipng({optimizationLevel: 7}),
		plugins.imagemin.svgo({plugins: [{removeViewBox: true}]})
	]));

	if (CONFIG.imagesSuffix) {
		pipe = pipe.pipe(plugins.rename({suffix: CONFIG.imagesSuffix}));
	}

	// versioning
	if (CONFIG.imagesVersioning) {
		pipe = pipe.pipe(plugins.rev());
	}

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return pipe;
};

let genImagesTask = function(i, pipe) {
	return function() {
		pipe = pipe || gulp.src(getImagesSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.images), {base: CONFIG.sourceDirectories[i]});
		pipe = processImages(pipe);
		pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
		pipe = runRevManifest(i, pipe);
		return pipe;
	};
};

let lintJs = function(pipe, lint_globals) {
	if (!CONFIG.jsLint) {
		return pipe;
	}

	//
	pipe = pipe.pipe(plugins.plumber());

	// init lint options
	let lintOptions = CONFIG.jsLintOptions;
	if (typeof lintOptions !== 'object') {
		lintOptions = {};
	}
	if (typeof lintOptions.configFile === 'undefined') {
		lintOptions.configFile = 'eslint.js';
	}
	if (lint_globals || typeof lintOptions.globals === 'undefined') {
		lintOptions.globals = CONFIG.jsLintGlobals;
	}
	if (typeof lintOptions.envs === 'undefined') {
		lintOptions.envs = CONFIG.jsLintEnvs;
	}
	if (CONFIG.jsLintEs6) {
		lintOptions.parserOptions = {
			ecmaVersion: 6,
		};
		lintOptions.envs.push('es6');
	}

	// check for errors
	pipe = pipe
		.pipe(plugins.eslint(lintOptions))
		//.pipe(plugins.eslint.formatEach('compact', process.stderr))
		.pipe(plugins.eslint.format())
	;

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return pipe;
};

let optimizeJs = function(pipe) {
	if (!CONFIG.jsOptimization) {
		return pipe;
	}

	pipe = pipe.pipe(plugins.flatmap(function(pipe, file) {
		//
		pipe = pipe.pipe(plugins.plumber());

		//
		let outputName = './' + file.relative;
		let cfg = CONFIG.jsOptimizationConfig;
		cfg.js_output_file = outputName;
		pipe = pipe.pipe(closureCompiler(cfg));

		//
		pipe = pipe.pipe(plugins.plumber.stop());

		return pipe;
	}));

	return pipe;
};

let processJs = function(pipe, fromTs) {
	if (typeof fromTs === 'undefined') {
		fromTs = false;
	}

	//
	pipe = pipe.pipe(plugins.plumber());

	if (CONFIG.production) {
		// optimize
		if (!fromTs || CONFIG.jsOptimizationOnTs) {
			pipe = pipe.pipe(plugins.plumber.stop());
			pipe = optimizeJs(pipe);
			pipe = pipe.pipe(plugins.plumber());
		}

		//  strip debugging instructions
		pipe = pipe.pipe(plugins.stripDebug());

		//  minify js
		if (CONFIG.jsUglify) {
			pipe = pipe.pipe(plugins.uglify({output: {comments: CONFIG.jsOptimization ? 'all' : 'some'}}));
		}
	}

	// beautify js if necessary
	if ((CONFIG.production && CONFIG.jsBeautify) || (!CONFIG.production && CONFIG.jsBeautifyDev)) {
		pipe = pipe
			.pipe(plugins.jsbeautify())
		;
	}

	// rename: add suffix
	if (CONFIG.jsSuffix) {
		pipe = pipe.pipe(plugins.rename({suffix: CONFIG.jsSuffix}));
	}

	// versioning
	if (CONFIG.jsVersioning) {
		pipe = pipe.pipe(plugins.rev());
	}

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return pipe;
};

let genJsTask = function(i, pipe) {
	return function() {
		pipe = pipe || gulp.src(getJsSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.js), {base: CONFIG.sourceDirectories[i]});
		if (!CONFIG.production) {
			pipe = pipe.pipe(plugins.sourcemaps.init());
		}
		pipe = lintJs(pipe);
		pipe = processJs(pipe);
		if (!CONFIG.production) {
			pipe = pipe.pipe(plugins.sourcemaps.write());
		}
		pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
		pipe = runRevManifest(i, pipe);
		return pipe;
	};
};

let processJsLib = function(pipe, outputName) {
	//
	pipe = pipe.pipe(plugins.plumber());

	//
	pipe = pipe.pipe(plugins.concat(outputName));

	// ensure the lib doesn't pollute global window namespace
	pipe = pipe.pipe(plugins.insert.prepend('(function(){ '));
	pipe = pipe.pipe(plugins.insert.append(' })();'));

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return processJs(pipe);
};

let runJsLibTask = function(dirName, i, pipe) {
	let srcDir = CONFIG.sourceDirectories[i] + CONFIG.directories.jsLibs + dirName;
	pipe = pipe || gulp.src(getJsLibsSelector(srcDir + '/'), {base: srcDir});
	if (!CONFIG.production) {
		pipe = pipe.pipe(plugins.sourcemaps.init());
	}
	pipe = lintJs(pipe);
	pipe = processJsLib(pipe, dirName + '.js');
	if (!CONFIG.production) {
		pipe = pipe.pipe(plugins.sourcemaps.write());
	}
	pipe = pipe.pipe(plugins.rename({ dirname: CONFIG.directories.jsLibs }));
	pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
	pipe = runRevManifest(i, pipe);
	return pipe;
};

let genJsLibsTask = function(i) {
	return function(done) {
		let directories = getSubDirectories(CONFIG.sourceDirectories[i] + CONFIG.directories.jsLibs);
		let tasks = [];
		for (let k in directories) {
			tasks.push(runJsLibTask(directories[k], i));
		}
		if (!tasks.length) {
			done();
		} else {
			return mergeStream(tasks);
		}
	};
};

let processTsLib = function(pipe, tsConfigPath, outputName) {
	//
	pipe = pipe.pipe(plugins.plumber());

	//
	let tsConfig = {};
	tsConfig.sourceMap = false;
	tsConfig.outFile = outputName;
	if (fs.existsSync(tsConfigPath)) {
		let tsProject = plugins.typescript.createProject(tsConfigPath, tsConfig);
		pipe = pipe.pipe(tsProject());
	} else {
		pipe = pipe.pipe(plugins.typescript(tsConfig));
	}

	// ensure the lib doesn't pollute global window namespace
	pipe = pipe.pipe(plugins.insert.prepend('(function(){ '));
	pipe = pipe.pipe(plugins.insert.append(' })();'));

	//
	pipe = pipe.pipe(plugins.plumber.stop());

	return processJs(pipe, true);
};

let runTsLibTask = function(dirName, i, pipe) {
	let srcDir = CONFIG.sourceDirectories[i] + CONFIG.directories.tsLibs + dirName;
	pipe = pipe || gulp.src(getTsLibsSelector(srcDir + '/'), {base: srcDir});
	if (!CONFIG.production) {
		pipe = pipe.pipe(plugins.sourcemaps.init());
	}
	pipe = processTsLib(pipe, path.resolve(__dirname, srcDir + '/tsconfig.json'), dirName + '.js');
	if (!CONFIG.production) {
		pipe = pipe.pipe(plugins.sourcemaps.write());
	}
	pipe = pipe.pipe(plugins.rename({ dirname: CONFIG.directories.tsLibs }));
	pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
	pipe = runRevManifest(i, pipe);
	return pipe;
};

let genTsLibsTask = function(i) {
	return function(done) {
		let directories = getSubDirectories(CONFIG.sourceDirectories[i] + CONFIG.directories.tsLibs);
		let tasks = [];
		for (let k in directories) {
			tasks.push(runTsLibTask(directories[k], i));
		}
		if (!tasks.length) {
			done();
		} else {
			return mergeStream(tasks);
		}
	};
};

let processVendor = function(pipe) {
	// nothing
	return pipe;
};

let genVendorTask = function(i, pipe) {
	return function() {
		pipe = pipe || gulp.src(getVendorSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.vendor), {base: CONFIG.sourceDirectories[i]});
		pipe = processVendor(pipe);
		pipe = pipe.pipe(gulp.dest(CONFIG.distDirectories[i]));
		pipe = runRevManifest(i, pipe);
		return pipe;
	};
};

let watchSourceDirectory = function(i) {
	let checkUnlink = function (event, epath, dist) {
		if (!event || (event != 'unlink' && event != 'unlinkDir') || !epath) {
			return false;
		}
		plugins.util.log('File deleted: ' + epath);
		plugins.util.log('For security reasons, the file was not deleted in dist directory. Run gulp clean if you want to clean it.');
		//gulp.src(dist + e.relative, {read: false}).pipe(plugins.clean());
		return true;
	};

	let checkOtherEventTypes = function (event, epath) {
		if (!event || event === 'error') {
			plugins.util.log('Error when watching files ...');
			return false;
		} else if (event === 'ready') {
			//
		}
		return true;
	};

	let extractSrcName = function(epath, distDir) {
		if (!epath) {
			return null;
		}
		let p = epath.indexOf(distDir);
		if (p === -1) {
			return null;
		}
		return epath.substr(p + distDir.length);
	};

	let sourceCurrentFile = function (epath, distDir, i) {
		if (!epath) {
			return;
		}
		try {
			if (fs.lstatSync(epath).isDirectory()) {
				return;
			}
		} catch(e) {
			return;
		}
		let extr = extractSrcName(epath, distDir);
		if (!extr) {
			return;
		}
		return gulp.src(extr, {base: CONFIG.sourceDirectories[i]});
	};

	plugins.util.log('Watching: ' + CONFIG.sourceDirectories[i]);
	let watchCss = function (event, epath) {
		let distDir = CONFIG.distDirectories[i] + CONFIG.directories.css;
		if (checkUnlink(event, epath, distDir)) {
			return;
		}
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}
		let r = (genCssTask(i, sourceCurrentFile(epath, distDir, i)))();
		plugins.util.log('Watch task ended for Css file: ' + epath);
		return r;
	};
	let watchLess = function (event, epath) {
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}
		let r = (genLessTask(i))();
		plugins.util.log('Watch task ended for Less in directory: ' + CONFIG.sourceDirectories[i] + CONFIG.directories.css);
		return r;
	};
	let watchSass = function (event, epath) {
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}
		let r = (genSassTask(i))();
		plugins.util.log('Watch task ended for Sass in directory: ' + CONFIG.sourceDirectories[i] + CONFIG.directories.css);
		return r;
	};
	let watchFonts = function (event, epath) {
		let distDir = CONFIG.distDirectories[i] + CONFIG.directories.fonts;
		if (checkUnlink(event, epath, distDir)) {
			return;
		}
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}
		let r = (genFontsTask(i, sourceCurrentFile(epath, distDir, i)))();
		plugins.util.log('Watch task ended for Font file: ' + epath);
		return r;
	};
	let watchImages = function (event, epath) {
		let distDir = CONFIG.distDirectories[i] + CONFIG.directories.images;
		if (checkUnlink(event, epath, distDir)) {
			return;
		}
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}
		let r = (genImagesTask(i, sourceCurrentFile(epath, distDir, i)))();
		plugins.util.log('Watch task ended for image file: ' + epath);
		return r;
	};
	let watchJs = function (event, epath) {
		let distDir = CONFIG.distDirectories[i] + CONFIG.directories.js;
		if (checkUnlink(event, epath, distDir)) {
			return;
		}
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}
		let r = (genJsTask(i, sourceCurrentFile(epath, distDir, i)))();
		plugins.util.log('Watch task ended for Js file: ' + epath);
		return r;
	};
	let watchJsLib = function (event, epath) {
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}

		let srcDir = CONFIG.sourceDirectories[i] + CONFIG.directories.jsLibs;
		let distDir = CONFIG.distDirectories[i] + CONFIG.directories.jsLibs;

		let srcDirRp = fs_rp.realpathSync(srcDir) + '/';

		if (!epath.startsWith(srcDirRp)) {
			plugins.util.log('File modified in unknown source directory, running jsLibs task for all subdirectories in: ' + CONFIG.sourceDirectories[i]);
			let r = genJsLibsTask(i)();
			plugins.util.log('Watch task ended for all JS libraries in: ' + CONFIG.sourceDirectories[i]);
			return r;
		}

		let dirName = epath.substr(srcDirRp.length);
		let p = dirName.indexOf('/');
		if (p == -1 || p == 0) {
			plugins.util.log('Cannot extract source directory, running jsLibs task for all subdirectories in: ' + CONFIG.sourceDirectories[i]);
			let r = genJsLibsTask(i)();
			plugins.util.log('Watch task ended for all JS libraries in: ' + CONFIG.sourceDirectories[i]);
			return r;
		}
		dirName = dirName.substr(0, p);

		let r = runJsLibTask(dirName, i);
		plugins.util.log('Watch task ended for Js library: ' + srcDir + dirName);
		return r;
	};
	let watchTsLib = function (event, epath) {
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}

		let srcDir = CONFIG.sourceDirectories[i] + CONFIG.directories.tsLibs;
		let distDir = CONFIG.distDirectories[i] + CONFIG.directories.tsLibs;

		let srcDirRp = fs_rp.realpathSync(srcDir) + '/';

		if (!epath.startsWith(srcDirRp)) {
			plugins.util.log('File modified in unknown source directory, running tsLibs task for all subdirectories in: ' + CONFIG.sourceDirectories[i]);
			let r = genTsLibsTask(i)();
			plugins.util.log('Watch task ended for all TS libraries in: ' + CONFIG.sourceDirectories[i]);
			return r;
		}

		let dirName = epath.substr(srcDirRp.length);
		let p = dirName.indexOf('/');
		if (p == -1 || p == 0) {
			plugins.util.log('Cannot extract source directory, running tsLibs task for all subdirectories in: ' + CONFIG.sourceDirectories[i]);
			let r = genTsLibsTask(i)();
			plugins.util.log('Watch task ended for all TS libraries in: ' + CONFIG.sourceDirectories[i]);
			return r;
		}
		dirName = dirName.substr(0, p);

		let r = runTsLibTask(dirName, i);
		plugins.util.log('Watch task ended for TS library: ' + srcDir + dirName);
		return r;
	};
	let watchVendor = function (event, epath) {
		let distDir = CONFIG.distDirectories[i] + CONFIG.directories.vendor;
		if (checkUnlink(event, epath, distDir)) {
			return;
		}
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}
		let r = (genVendorTask(i, sourceCurrentFile(epath, distDir, i)))();
		plugins.util.log('Watch task ended for vendor file: ' + epath);
		return r;
	};
	let watchVersioning = function (event, epath) {
		if (checkUnlink(event, epath)) {
			return;
		}
		if (!checkOtherEventTypes(event, epath)) {
			return;
		}
		let r = (genApplyRev(i))();
		plugins.util.log('Watch task ended for versioning related files: ' + epath);
		return r;
	};

	let w;
	let events = ['change', 'add', 'addDir', 'unlink', 'unlinkDir', 'ready'];
	w = gulp.watch(getCssSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.css));
	w.on('all', watchCss);
	w = gulp.watch(getLessSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.css, true));
	w.on('all', watchLess);
	w = gulp.watch(getSassSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.css, true));
	w.on('all', watchSass);
	w = gulp.watch(getFontsSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.fonts));
	w.on('all', watchFonts);
	w = gulp.watch(getImagesSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.images));
	w.on('all', watchImages);
	w = gulp.watch(getJsSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.js));
	w.on('all', watchJs);
	w = gulp.watch(getExtensionsSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.jsLibs + '**/'));
	w.on('all', watchJsLib);
	w = gulp.watch(getExtensionsSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.tsLibs + '**/'));
	w.on('all', watchTsLib);
	w = gulp.watch(getVendorSelector(CONFIG.sourceDirectories[i] + CONFIG.directories.vendor));
	w.on('all', watchVendor);
	w = gulp.watch(path.resolve(CONFIG.distDirectories[i], CONFIG.versioningManifestName));
	w.on('all', watchVersioning);
};

/*
 * Tasks
 * #####################################################################
 */

// Register regular tasks
let cssTasks = [];
let lessTasks = [];
let sassTasks = [];
let fontsTasks = [];
let imagesTasks = [];
let jsTasks = [];
let jsLibsTasks = [];
let tsLibsTasks = [];
let vendorTasks = [];
let versioningTasks = [];
let i = 0;
for (let k in CONFIG.sourceDirectories) {
	cssTasks.push(registerTask('css' + i, genCssTask(k)));
	lessTasks.push(registerTask('less' + i, genLessTask(k)));
	sassTasks.push(registerTask('sass' + i, genSassTask(k)));
	fontsTasks.push(registerTask('fonts' + i, genFontsTask(k)));
	imagesTasks.push(registerTask('images' + i, genImagesTask(k)));
	jsTasks.push(registerTask('js' + i, genJsTask(k)));
	jsLibsTasks.push(registerTask('jsLibs' + i, genJsLibsTask(k)));
	tsLibsTasks.push(registerTask('tsLibs' + i, genTsLibsTask(k)));
	vendorTasks.push(registerTask('vendor' + i, genVendorTask(k)));
	versioningTasks.push(registerTask('versioning' + i, genApplyRev(k)));
	++i;
}
registerTask('css', cssTasks);
registerTask('less', lessTasks);
registerTask('sass', sassTasks);
registerTask('fonts', fontsTasks);
registerTask('images', imagesTasks);
registerTask('js', jsTasks);
registerTask('jsLibs', jsLibsTasks);
registerTask('tsLibs', tsLibsTasks);
registerTask('vendor', vendorTasks);
registerTask('versioning', versioningTasks);

// Register special tasks
gulp.task('setDev', function(done) {
	CONFIG.production = false;
	done();
});

gulp.task('setProduction', function(done) {
	CONFIG.production = true;
	done();
});

let stateTask = function(done) {
	plugins.util.log((CONFIG.production ? 'Generating PRODUCTION assets' : 'Generating DEVELOPMENT assets'));
	done();
};
gulp.task('state', stateTask);

gulp.task('default', gulp.series('state', gulp.series(gulp.parallel('css', 'less', 'sass', 'fonts', 'images', 'js', 'jsLibs', 'tsLibs', 'vendor'), 'versioning')));

gulp.task('development', gulp.series('setDev', 'default'));
gulp.task('production', gulp.series('setProduction', 'default'));

gulp.task('clean', function() {
	let tasks = [];
	for (let i in CONFIG.distDirectories) {
		let dir = CONFIG.distDirectories[i];
		if (dir && dir.charAt(0) == '/') {
			plugins.util.log('Skipping directory (shoud not be absolute!): ' + dir);
			continue;
		}
		if (!dir) {
			plugins.util.log('Skipping empty directory');
			continue;
		}
		dir = path.resolve(__dirname, dir);
		plugins.util.log('Wiping directory: ' + dir);
		process.chdir(path.dirname(dir));
		tasks.push(gulp.src(dir, {read: false, allowEmpty: true}).pipe(plugins.clean()));
	}
	return mergeStream(tasks);
});

// Register watch tasks
let watchTask = function (done) {
	for (let i in CONFIG.sourceDirectories)
		watchSourceDirectory(i);
	done();
};
gulp.task('watch', watchTask);
gulp.task('developmentWatch', gulp.series('setDev', 'watch'));
gulp.task('productionWatch', gulp.series('setProduction', 'watch'));
