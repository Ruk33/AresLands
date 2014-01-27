/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.language = 'es';

	config.extraPlugins = 'bbcode';

	config.disableObjectResizing = true;

	config.scayt_autoStartup = true;
	
	config.removeDialogTabs = 'image:advanced;image:Link;link:advanced;link:upload';
    config.linkShowTargetTab = false;
	
	config.disableNativeSpellChecker = false;

	config.removePlugins = 'liststyle,menubutton,contextmenu,elementspath,colordialog,div,find,flash,forms,iframe,pastefromword,preview,scayt,table,tabletools,templates,wsc';
	config.toolbar = [
		['Font', 'FontSize'],
		['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'],
		['TextColor', 'BGColor'],
		['Link', 'Unlink'],
		['Image'],
		['About']
	];
};
