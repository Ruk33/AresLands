/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.language = 'es';

	config.extraPlugins = 'bbcode';

	config.disableObjectResizing = true;

	config.scayt_autoStartup = true;

	config.removePlugins = 'elementspath,colordialog,div,find,flash,forms,iframe,pastefromword,preview,scayt,table,tabletools,templates,wsc';
	config.toolbar = [
		['Bold', 'Italic', 'Underline'],
		['Link', 'Unlink'],
		['Blockquote', 'Image'],
		['About']
	];
};
