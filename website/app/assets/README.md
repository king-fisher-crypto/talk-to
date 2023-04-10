This folder contains all non-compiled and non-minified assets.

Gulp is used for assets processing.

# Installation
- Install npm:
	- Ubuntu / Debian: `sudo apt-get install nodejs` ;
	- ArchLinux: `sudo pacman -S nodejs` ;
	- Centos: `sudo yum install nodejs`.
- Run: `npm install`

# Assets Generation
- During development to watch changes for automatic generation: `npm run watch`
- Generate all assets (for development): `npm run dev`
- Generate production assets: `npm run production`

# Conventions JS / TS
We follow Google conventions for javascript and typescript.
https://google.github.io/styleguide/jsguide.html

Summary:
- use spaces for indentation (no tabulations please);
- brackets should be in the same line as function declarations;
- function naming is `camelCase`;
- variable naming is `snake_case`.

# Conventions CSS / SASS
We also follow google conventions for HTML, CSS and SASS.
https://google.github.io/styleguide/htmlcssguide.html

Summary:
- avoid type qualification in stylesheets whenever possible: `.error` is preferable over `div.error`;
- avoid adding units after zeros: `margin: 0` and not `margin: 0px`;
- do not put leading zeros: `font-size: .8em` and not `font-size: 0.8em`;
- use hyphens instead of underscore to separate words in class or id names: `.ads-simple` and not `.ads_simple`;
- add space before property values: `font-weight: bold` and not `font-weight:bold`;
- same for css blocks: `.video {` and not `.video{` (no line-break!);
- one declaration per line;
- use single quote `'` rather than double quotes `"` (for example in `url()`);
- use block comments to group declarations when possible: `/* Header */`, `/* Gallery */`, etc.

# Other remarks and good practices
- Please, create separate git commits for reformating and code style fixing. Coding commits should be minimal!
- Please, use ENGLISH for naming and comments to keep the code base maintainable in the future.
- Avoid poluting global namespace when writing JS as much as possible.
