module.exports = {
	// Set the line length to a reasonable value (e.g., 100) or adjust as needed.
	printWidth: 100,

	// Use tabs for indentation (Laravel's convention) rather than spaces.
	useTabs: true,

	// Set the tab width to the number of spaces per tab (Laravel's default is usually 4).
	tabWidth: 4,

	// Use single quotes for strings (Vue's convention) unless double quotes are needed within the string.
	singleQuote: true,

	// Avoid using semicolons at the end of lines (Vue's convention).
	semi: true,

	// Use trailing commas when multi-line (Vue's convention) to avoid git diffs with fewer lines.
	trailingComma: 'all',

	// Specify the parser for the JavaScript/TypeScript code in Vue files.
	// Assuming you have installed the appropriate parser for Vue files.
	vueIndentScriptAndStyle: true,

	// Override the default parser to handle PHP files as well (using the 'prettier-plugin-php' package).
	// Assuming you have installed the 'prettier-plugin-php' package.
	overrides: [
		{
			files: '*.php',
			options: {
				parser: 'php',
			},
		},
		{
			files: '*.vue',
			options: {
				parser: 'vue',
			},
		}
	],

	vue: {
		// Set the parser for the JavaScript/TypeScript code in Vue files.
		parser: 'babel',

		// Set the indentation level for the HTML inside Vue files.
		// Here, we use 4 spaces to match the provided format.
		htmlWhitespaceSensitivity: 'ignore',
		htmlWhitespaceSensitivity: 'strict',

		// Specify the attributes that should not be auto-formatted.
		// In this case, we're excluding the 'class' attribute to maintain the original format.
		// Add any other attributes that you want to exclude from auto-formatting.
		ignoredAttributes: ['class', 'for'],

		// Set the default indent for Vue files to 4 spaces.
		// This will be used for the JavaScript and CSS parts of the Vue files.
		scriptIndent: 4,
		styleIndent: 4,
	},
}
