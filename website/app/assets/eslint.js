module.exports = {
	"extends": "google",
	"rules": {
		"array-bracket-spacing": ["error", "never"],
		"camelcase": 0,
		"comma-dangle": ["error", "never"],
		"curly": 0,
		"eqeqeq": ["error", "smart"],
        "indent": ["warn", 4],
		"linebreak-style": 0,
		"newline-per-chained-call": ["error", {"ignoreChainWithDepth": 4}],
		"no-console": 0,
		"no-invalid-this": 0,
		"no-redeclare": ["error", {"builtinGlobals": true}],
		"no-var": 0,
		"object-curly-newline": 0,
		"object-shorthand": 0,
		"prefer-arrow-callback": 0,
		"quote-props": 0,
		"semi": ["error", "always"],
		"space-before-function-paren": ["error", "never"],
        "max-len": ["warn", { "code": 120 }]
    }
};
