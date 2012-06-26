// ----------------------------------------------------------------------------
// markItUp! Universal MarkUp Engine, JQuery plugin
// v 1.1.x
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2007-2011 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
(function($) {
	$.fn.markItUp = function(settings, extraSettings) {
		var options, ctrlKey, shiftKey, altKey;
		ctrlKey = shiftKey = altKey = false;
	
		options = {	id:						'',
					nameSpace:				'',
					root:					'',
					previewInWindow:		'', // 'width=800, height=600, resizable=yes, scrollbars=yes'
					previewAutoRefresh:		true,
					previewPosition:		'after',
					previewTemplatePath:	'~/templates/preview.html',
					previewParser:			false,
					previewParserPath:		'',
					previewParserVar:		'data',
					previewScroll:			'bottom', // top, bottom
					resizeHandle:			true,
					beforeInsert:			'',
					afterInsert:			'',
					onEnter:				{},
					onShiftEnter:			{},
					onCtrlEnter:			{},
					onTab:					{},
					markupSet:			[	{ /* set */ } ]
				};
		$.extend(options, settings, extraSettings);

		// compute markItUp! path
		if (!options.root) {
			$('script').each(function(a, tag) {
				miuScript = $(tag).get(0).src.match(/(.*)jquery\.markitup(\.pack)?\.js$/);
				if (miuScript !== null) {
					options.root = miuScript[1];
				}
			});
		}

		return this.each(function() {
			var $$, textarea, levels, scrollPosition, caretPosition, caretOffset,
				clicked, hash, header, footer, previewWindow, template, iFrame, abort;
			$$ = $(this);
			textarea = this;
			levels = [];
			abort = false;
			scrollPosition = caretPosition = 0;
			caretOffset = -1;

			options.previewParserPath = localize(options.previewParserPath);
			options.previewTemplatePath = localize(options.previewTemplatePath);

			// apply the computed path to ~/
			function localize(data, inText) {
				if (inText) {
					return 	data.replace(/("|')~\//g, "$1"+options.root);
				}
				return 	data.replace(/^~\//, options.root);
			}

			// init and build editor
			function init() {
				id = ''; nameSpace = '';
				if (options.id) {
					id = 'id="'+options.id+'"';
				} else if ($$.attr("id")) {
					id = 'id="markItUp'+($$.attr("id").substr(0, 1).toUpperCase())+($$.attr("id").substr(1))+'"';

				}
				if (options.nameSpace) {
					nameSpace = 'class="'+options.nameSpace+'"';
				}
				$$.wrap('<div '+nameSpace+'></div>');
				$$.wrap('<div '+id+' class="markItUp"></div>');
				$$.wrap('<div class="markItUpContainer"></div>');
				$$.addClass("markItUpEditor");

				// add the header before the textarea
				header = $('<div class="markItUpHeader"></div>').insertBefore($$);
				$(dropMenus(options.markupSet)).appendTo(header);

				// add the footer after the textarea
				footer = $('<div class="markItUpFooter"></div>').insertAfter($$);

				// add the resize handle after textarea
				if (options.resizeHandle === true && $.browser.safari !== true) {
					resizeHandle = $('<div class="markItUpResizeHandle"></div>')
						.insertAfter($$)
						.bind("mousedown", function(e) {
							var h = $$.height(), y = e.clientY, mouseMove, mouseUp;
							mouseMove = function(e) {
								$$.css("height", Math.max(20, e.clientY+h-y)+"px");
								return false;
							};
							mouseUp = function(e) {
								$("html").unbind("mousemove", mouseMove).unbind("mouseup", mouseUp);
								return false;
							};
							$("html").bind("mousemove", mouseMove).bind("mouseup", mouseUp);
					});
					footer.append(resizeHandle);
				}

				// listen key events
				$$.keydown(keyPressed).keyup(keyPressed);
				
				// bind an event to catch external calls
				$$.bind("insertion", function(e, settings) {
					if (settings.target !== false) {
						get();
					}
					if (textarea === $.markItUp.focused) {
						markup(settings);
					}
				});

				// remember the last focus
				$$.focus(function() {
					$.markItUp.focused = this;
				});
			}

			// recursively build header with dropMenus from markupset
			function dropMenus(markupSet) {
				var ul = $('<ul></ul>'), i = 0;
				$('li:hover > ul', ul).css('display', 'block');
				$.each(markupSet, function() {
					var button = this, t = '', title, li, j;
					title = (button.key) ? (button.name||'')+' [Ctrl+'+button.key+']' : (button.name||'');
					key   = (button.key) ? 'accesskey="'+button.key+'"' : '';
					if (button.separator) {
						li = $('<li class="markItUpSeparator">'+(button.separator||'')+'</li>').appendTo(ul);
					} else {
						i++;
						for (j = levels.length -1; j >= 0; j--) {
							t += levels[j]+"-";
						}
						li = $('<li class="markItUpButton markItUpButton'+t+(i)+' '+(button.className||'')+'"><a href="" '+key+' title="'+title+'">'+(button.name||'')+'</a></li>')
						.bind("contextmenu", function() { // prevent contextmenu on mac and allow ctrl+click
							return false;
						}).click(function() {
							return false;
						}).bind("focusin", function(){
                            $$.focus();
						}).mouseup(function() {
							if (button.call) {
								eval(button.call)();
							}
							setTimeout(function() { markup(button) },1);
							return false;
						}).hover(function() {
								$('> ul', this).show();
								$(document).one('click', function() { // close dropmenu if click outside
										$('ul ul', header).hide();
									}
								);
							}, function() {
								$('> ul', this).hide();
							}
						).appendTo(ul);
						if (button.dropMenu) {
							levels.push(i);
							$(li).addClass('markItUpDropMenu').append(dropMenus(button.dropMenu));
						}

						if (button.key == 'p') {
							var inp = $('<input id="mkd-input-file" class="hide" type="file" multiple="" />').insertAfter(li);
							li.unbind('click').click(function() {
								inp.click();
								return false;
							});
							li.unbind('mouseup');
						}
					}
				}); 
				levels.pop();
				return ul;
			}

			// markItUp! markups
			function magicMarkups(string) {
				if (string) {
					string = string.toString();
					string = string.replace(/\(\!\(([\s\S]*?)\)\!\)/g,
						function(x, a) {
							var b = a.split('|!|');
							if (altKey === true) {
								return (b[1] !== undefined) ? b[1] : b[0];
							} else {
								return (b[1] === undefined) ? "" : b[0];
							}
						}
					);
					// [![prompt]!], [![prompt:!:value]!]
					string = string.replace(/\[\!\[([\s\S]*?)\]\!\]/g,
						function(x, a) {
							var b = a.split(':!:');
							if (abort === true) {
								return false;
							}
							value = prompt(b[0], (b[1]) ? b[1] : '');
							if (value === null) {
								abort = true;
							}
							return value;
						}
					);
					return string;
				}
				return "";
			}

			// prepare action
			function prepare(action) {
				if ($.isFunction(action)) {
					action = action(hash);
				}
				return magicMarkups(action);
			}

			// build block to insert
			function build(string) {
				var openWith 			= prepare(clicked.openWith);
				var placeHolder 		= prepare(clicked.placeHolder);
				var replaceWith 		= prepare(clicked.replaceWith);
				var closeWith 			= prepare(clicked.closeWith);
				var openBlockWith 		= prepare(clicked.openBlockWith);
				var closeBlockWith 		= prepare(clicked.closeBlockWith);
				var multiline 			= clicked.multiline;
				
				if (replaceWith !== "") {
					block = openWith + replaceWith + closeWith;
				} else if (selection === '' && placeHolder !== '') {
					block = openWith + placeHolder + closeWith;
				} else {
					string = string || selection;

					var lines = [string], blocks = [];
					
					if (multiline === true) {
						lines = string.split(/\r?\n/);
					}
					
					for (var l = 0; l < lines.length; l++) {
						line = lines[l];
						var trailingSpaces;
						if (trailingSpaces = line.match(/ *$/)) {
							blocks.push(openWith + line.replace(/ *$/g, '') + closeWith + trailingSpaces);
						} else {
							blocks.push(openWith + line + closeWith);
						}
					}
					
					block = blocks.join("\n");
				}

				block = openBlockWith + block + closeBlockWith;

				return {	block:block, 
							openWith:openWith, 
							replaceWith:replaceWith, 
							placeHolder:placeHolder,
							closeWith:closeWith
					};
			}

			// define markup to insert
			function markup(button) {
				var len, j, n, i;
				hash = clicked = button;
				get();
				$.extend(hash, {	line:"", 
						 			root:options.root,
									textarea:textarea, 
									selection:(selection||''), 
									caretPosition:caretPosition,
									ctrlKey:ctrlKey, 
									shiftKey:shiftKey, 
									altKey:altKey
								}
							);
				// callbacks before insertion
				prepare(options.beforeInsert);
				prepare(clicked.beforeInsert);
				if ((ctrlKey === true && shiftKey === true) || button.multiline === true) {
					prepare(clicked.beforeMultiInsert);
				}			
				$.extend(hash, { line:1 });

				if ((ctrlKey === true && shiftKey === true)) {
					lines = selection.split(/\r?\n/);
					for (j = 0, n = lines.length, i = 0; i < n; i++) {
						if ($.trim(lines[i]) !== '') {
							$.extend(hash, { line:++j, selection:lines[i] } );
							lines[i] = build(lines[i]).block;
						} else {
							lines[i] = "";
						}
					}

					string = { block:lines.join('\n')};
					start = caretPosition;
					len = string.block.length + (($.browser.opera) ? n-1 : 0);
				} else if (ctrlKey === true) {
					string = build(selection);
					start = caretPosition + string.openWith.length;
					len = string.block.length - string.openWith.length - string.closeWith.length;
					len = len - (string.block.match(/ $/) ? 1 : 0);
					len -= fixIeBug(string.block);
				} else if (shiftKey === true) {
					string = build(selection);
					start = caretPosition;
					len = string.block.length;
					len -= fixIeBug(string.block);
				} else {
					string = build(selection);
					start = caretPosition + string.block.length ;
					len = 0;
					start -= fixIeBug(string.block);
				}
				if ((selection === '' && string.replaceWith === '')) {
					caretOffset += fixOperaBug(string.block);
					
					start = caretPosition + string.openWith.length;
					len = string.block.length - string.openWith.length - string.closeWith.length;

					caretOffset = $$.val().substring(caretPosition,  $$.val().length).length;
					caretOffset -= fixOperaBug($$.val().substring(0, caretPosition));
				}
				$.extend(hash, { caretPosition:caretPosition, scrollPosition:scrollPosition } );

				if (string.block !== selection && abort === false) {
					insert(string.block);
					set(start, len);
				} else {
					caretOffset = -1;
				}
				get();

				$.extend(hash, { line:'', selection:selection });

				// callbacks after insertion
				if ((ctrlKey === true && shiftKey === true) || button.multiline === true) {
					prepare(clicked.afterMultiInsert);
				}
				prepare(clicked.afterInsert);
				prepare(options.afterInsert);

				// refresh preview if opened
				if (previewWindow && options.previewAutoRefresh) {
					refreshPreview(); 
				}
																									
				// reinit keyevent
				shiftKey = altKey = ctrlKey = abort = false;
			}

			// Substract linefeed in Opera
			function fixOperaBug(string) {
				if ($.browser.opera) {
					return string.length - string.replace(/\n*/g, '').length;
				}
				return 0;
			}
			// Substract linefeed in IE
			function fixIeBug(string) {
				if ($.browser.msie) {
					return string.length - string.replace(/\r*/g, '').length;
				}
				return 0;
			}
				
			// add markup
			function insert(block) {	
				if (document.selection) {
					var newSelection = document.selection.createRange();
					newSelection.text = block;
				} else {
					textarea.value =  textarea.value.substring(0, caretPosition)  + block + textarea.value.substring(caretPosition + selection.length, textarea.value.length);
				}
			}

			// set a selection
			function set(start, len) {
				if (textarea.createTextRange){
					// quick fix to make it work on Opera 9.5
					if ($.browser.opera && $.browser.version >= 9.5 && len == 0) {
						return false;
					}
					range = textarea.createTextRange();
					range.collapse(true);
					range.moveStart('character', start); 
					range.moveEnd('character', len); 
					range.select();
				} else if (textarea.setSelectionRange ){
					textarea.setSelectionRange(start, start + len);
				}
				textarea.scrollTop = scrollPosition;
				textarea.focus();
			}

			// get the selection
			function get() {
				textarea.focus();

				scrollPosition = textarea.scrollTop;
				if (document.selection) {
					selection = document.selection.createRange().text;
					if ($.browser.msie) { // ie
						var range = document.selection.createRange(), rangeCopy = range.duplicate();
						rangeCopy.moveToElementText(textarea);
						caretPosition = -1;
						while(rangeCopy.inRange(range)) {
							rangeCopy.moveStart('character');
							caretPosition ++;
						}
					} else { // opera
						caretPosition = textarea.selectionStart;
					}
				} else { // gecko & webkit
					caretPosition = textarea.selectionStart;

					selection = textarea.value.substring(caretPosition, textarea.selectionEnd);
				} 
				return selection;
			}

			// open preview window
			function preview() {
				if (!previewWindow || previewWindow.closed) {
					if (options.previewInWindow) {
						previewWindow = window.open('', 'preview', options.previewInWindow);
						$(window).unload(function() {
							previewWindow.close();
						});
					} else {
						iFrame = $('<iframe class="markItUpPreviewFrame"></iframe>');
						if (options.previewPosition == 'after') {
							iFrame.insertAfter(footer);
						} else {
							iFrame.insertBefore(header);
						}	
						previewWindow = iFrame[iFrame.length - 1].contentWindow || frame[iFrame.length - 1];
					}
				} else if (altKey === true) {
					if (iFrame) {
						iFrame.remove();
					} else {
						previewWindow.close();
					}
					previewWindow = iFrame = false;
				}
				if (!options.previewAutoRefresh) {
					refreshPreview(); 
				}
				if (options.previewInWindow) {
					previewWindow.focus();
				}
			}

			// refresh Preview window
			function refreshPreview() {
 				renderPreview();
			}

			function renderPreview() {		
				var phtml;
				if (options.previewParser && typeof options.previewParser === 'function') {
					var data = options.previewParser( $$.val() );
					writeInPreview( localize(data, 1) ); 
				} else if (options.previewParserPath !== '') {
					$.ajax({
						type: 'POST',
						dataType: 'text',
						global: false,
						url: options.previewParserPath,
						data: options.previewParserVar+'='+encodeURIComponent($$.val()),
						success: function(data) {
							writeInPreview( localize(data, 1) ); 
						}
					});
				} else {
					if (!template) {
						$.ajax({
							url: options.previewTemplatePath,
							dataType: 'text',
							global: false,
							success: function(data) {
								writeInPreview( localize(data, 1).replace(/<!-- content -->/g, $$.val()) );
							}
						});
					}
				}
				return false;
			}
			
			function writeInPreview(data) {
				if (previewWindow.document) {			
					previewWindow.document.open();
					previewWindow.document.write(data);
					previewWindow.document.close();

					try {
						if (options.previewScroll == 'bottom')
							sp = previewWindow.document.documentElement.scrollHeight;
						else
							sp = previewWindow.document.documentElement.scrollTop
					} catch(e) {
						sp = 0;
					}	
					previewWindow.document.documentElement.scrollTop = sp;
				}
			}
			
			// set keys pressed
			function keyPressed(e) { 
				shiftKey = e.shiftKey;
				altKey = e.altKey;
				ctrlKey = (!(e.altKey && e.ctrlKey)) ? (e.ctrlKey || e.metaKey) : false;

				if (e.type === 'keydown') {
					if (ctrlKey === true) {
						li = $('a[accesskey="'+String.fromCharCode(e.keyCode)+'"]', header).parent('li');
						if (li.length !== 0) {
							ctrlKey = false;
							setTimeout(function() {
								li.triggerHandler('mouseup');
							},1);
							return false;
						}
					}
					if (e.keyCode === 13 || e.keyCode === 10) { // Enter key
						if (ctrlKey === true) {  // Enter + Ctrl
							ctrlKey = false;
							markup(options.onCtrlEnter);
							return options.onCtrlEnter.keepDefault;
						} else if (shiftKey === true) { // Enter + Shift
							shiftKey = false;
							markup(options.onShiftEnter);
							return options.onShiftEnter.keepDefault;
						} else { // only Enter
							markup(options.onEnter);
							return options.onEnter.keepDefault;
						}
					}
					if (e.keyCode === 9) { // Tab key
						if (shiftKey == true || ctrlKey == true || altKey == true) {
							return false; 
						}
						if (caretOffset !== -1) {
							get();
							caretOffset = $$.val().length - caretOffset;
							set(caretOffset, 0);
							caretOffset = -1;
							return false;
						} else {
							markup(options.onTab);
							return options.onTab.keepDefault;
						}
					}
				}
			}

			init();
		});
	};

	$.fn.markItUpRemove = function() {
		return this.each(function() {
				var $$ = $(this).unbind().removeClass('markItUpEditor');
				$$.parent('div').parent('div.markItUp').parent('div').replaceWith($$);
			}
		);
	};

	$.markItUp = function(settings) {
		var options = { target:false };
		$.extend(options, settings);
		if (options.target) {
			return $(options.target).each(function() {
				$(this).focus();
				$(this).trigger('insertion', [options]);
			});
		} else {
			$('textarea').trigger('insertion', [options]);
		}
	};
})(jQuery);
// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
myMarkdownSettings = {
    nameSpace:          'markdown', // Useful to prevent multi-instances CSS conflict
    previewPosition: 'before',
    //previewInWindow: 'width=800, height=600, resizable=yes, scrollbars=yes',
    previewParserPath:  '/paper/preview',
    previewScroll: 'bottom',
    previewParserVar:   's',
    onShiftEnter:       {keepDefault:false, openWith:'\n\n'},
    markupSet: [
        {name:'First Level Heading', key:"1", placeHolder:'这里写标题内容...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '=') } },
        {name:'Second Level Heading', key:"2", placeHolder:'这里写标题内容...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '-') } },
        {name:'Heading 3', key:"3", openWith:'### ', placeHolder:'这里写标题内容...' },
        {name:'Heading 4', key:"4", openWith:'#### ', placeHolder:'这里写标题内容...' },
        {name:'Heading 5', key:"5", openWith:'##### ', placeHolder:'这里写标题内容...' },
        {name:'Heading 6', key:"6", openWith:'###### ', placeHolder:'这里写标题内容...' },
        {separator:'---------------' },        
        {name:'Bold', key:"B", openWith:'**', closeWith:'**'},
        {name:'Italic', key:"I", openWith:'_', closeWith:'_'},
        {separator:'---------------' },
        {name:'Bulleted List', openWith:'- ' },
        {name:'Numeric List', openWith:function(markItUp) {
            return markItUp.line+'. ';
        }},
        {separator:'---------------' },
        //{name:'Picture', key:"P", replaceWith:'![[![图片描述]!]]([![图片连接:!:http://]!] "[![图片标题]!]")'},
        {name:'Picture', key:"p", replaceWith:'![aaa](bbb "ccc")'},
        //{name:'Link', key:"L", openWith:'[', closeWith:']([![连接地址:!:http://]!] "[![连接标题]!]")', placeHolder:'这里写连接的说明文字...' },
        {name:'Link', key:"L", openWith:'[连接内容](http://', closeWith:' "说明文字")', placeHolder:'www.' },
        {separator:'---------------'},    
        {name:'Quotes', openWith:'> '},
        {name:'Code Block / Code', openWith:'(!(\t|!|`)!)', closeWith:'(!(`)!)'},
        {separator:'---------------'},
        {name:'Preview', call:'preview', className:"preview"}
    ]
}

// mIu nameSpace to avoid conflict.
miu = {
    markdownTitle: function(markItUp, char) {
        heading = '';
        n = $.trim(markItUp.selection||markItUp.placeHolder).length;
        for(i = 0; i < n; i++) {
            heading += char;
        }
        return '\n'+heading+'\n';
    }
}
/**
 * Uploader (for jQuery)
 * version: 1.0 (29/01/2012)
 * @requires jQuery v1.4.0 or later
 * @copyright 2012 Julien DENIAU
 */
if(typeof jQuery !== undefined){
	(function($){
		$.fn.uploader = function(params){
			// =============================== 
			// Settings 
			// =============================== 
			var options = $.extend({}, {
				dropZone: $(this),
				fileField: null,
				url: null,
				dataName: 'upfile',
				extraData: {},

				allowedExtension: 'jpeg, bmp, png, gif',
				showThumbnails : false,
				thumbnails : {
					div: null,
					width: null,
					height: null
				},

				maxFileSize: 0,
				progressBar: null,
				
				onFilesSelected: function() { return false; },
				onDragLeave: function() { return false; },
				onDragEnter: function() { return false; },
				onDragOver: function() { return false; },
				onDrop: function() { return false; },
				onUploadProgress: function(event) { return false; },
				beforeUpload: function() { return true; },
				afterUpload: function() { return false; },
				error: function(msg) { return false; }
			}, params);


			// =============================== 
			// Internal functions
			// =============================== 
			/**
			 * fileApiSupported check if the file api is supported
			 * 
			 * @return void
			 */
			function fileApiSupported() {
				return (window.File && window.FileReader && window.FileList);
			}

			/**
			 * onDragLeave 
			 * 
			 * @param event $event 
			 * @return void
			 */
			function onDragLeave(event) {
				event.preventDefault();
				event.stopPropagation();
				//you can remove a style from the drop zone
				return options.onDragLeave();
			}

			/**
			 * onDragEnter 
			 * 
			 * @param event $event 
			 * @return void
			 */
			function onDragEnter(event) {
				event.preventDefault();
				event.stopPropagation();
				//you can add a style to the drop zone
				return options.onDragEnter();
			}

			/**
			 * onDragOver 
			 * 
			 * @param event $event 
			 * @return void
			 */
			function onDragOver(event) {
				event.preventDefault();
				event.stopPropagation();
				event.originalEvent.dataTransfer.effectAllowed= "copy";
				event.originalEvent.dataTransfer.dropEffect = "copy";

				return options.onDragOver();
			}

			/**
			 * onDrop 
			 * 
			 * @param event $event 
			 * @return void
			 */
			function onDrop(event) {
				event.preventDefault();
				event.stopPropagation();
				addFiles(event.originalEvent.dataTransfer.files);

				return options.onDrop();
			}



			/**
			 * onUploadProgress 
			 * 
			 * @param event $event 
			 * @return void
			 */
			function onUploadProgress(event) {
				if (event.lengthComputable) {
					console.log(event.loaded + '/' + event.total);
				}

				return options.onUploadProgress(event);
			}

			/**
			 * uploadComplete 
			 * 
			 * @return void
			 */
			function uploadComplete(event) {
				return options.afterUpload(event.target.response);
			}

			/**
			 * uploadFailed 
			 * 
			 * @return void
			 */
			function uploadFailed() {
				return options.error('upload failed');
			}

			/**
			 * uploadCanceled 
			 * 
			 * @return void
			 */
			function uploadCanceled() {
				return options.error('upload canceled');
			}

			function boundaryVar(name, value, boundary) {
				var s = '--' + boundary + '\r\n';
				s += 'Content-Disposition: form-data; name="'+ name + '"\r\n\r\n';
				s += value;
				s += '\r\n';
				return s
			}

			function boundaryFile(name, file, boundary, body, xhr) {
				var reader = new FileReader();

				var s = body + '--' + boundary + '\r\n';
				s += 'Content-Disposition: form-data; name="'+name+'"; filename="' + file.name + '"\r\n';
				s += 'Content-Type: '+file.type+'\r\n\r\n';

				reader.onload = function(evt) {
					s += evt.target.result.split(',')[1] + '\r\n'
					s += '--' + boundary + '--';
					
					xhr.send(s);
				};

				/*
				 * don't use reader.readAsBinaryString(file) because reader returning large, bad data
				 * http://stackoverflow.com/questions/6133800/html5-file-api-readasbinarystring-reads-files-as-much-larger-different-than-fil
				 *
				 * readAsDataURL() will return base64 encoded data
				 * so, don't forget inform backend to decode it by
				 * body += boundaryVar('_upfile_data_type', 'dataurl', boundary);
				 */
				reader.readAsDataURL(file);
			}

			/**
			 * uploadFile upload the file
			 * 
			 * @param file $file 
			 * @return void
			 */
			function uploadFile(file) {
				var xhr = new XMLHttpRequest();

				//on s'abonne à l'événement progress pour savoir où en est l'upload
				if (xhr.upload && options.beforeUpload()) {
					/*
					 * we don't use
					 * xhr.open('PUT', xxx)
					 * because it will send only filedata, can't carry extraData
					 *
					 * we don't use
					 * xxx = new FormData(), xxx.append(file.name, file), xxx.send()
					 * because it will send only filedata, can't carry extraData
					 * and can't set boundary for backend parser use (rfc2338)
					 */
					var boundary = options.boundary || '----WaittingForBigml----',
					body = '';
					
					xhr.open('POST', options.url, true);

					// on s'abonne à tout changement de statut pour détecter
					// une erreur, ou la fin de l'upload
					//xhr.onreadystatechange = onStateChange; 

					/* event listners */
					xhr.upload.addEventListener('progress',	 onUploadProgress, false);
					xhr.addEventListener("load", uploadComplete, false);
					xhr.addEventListener("error", uploadFailed, false);
					xhr.addEventListener("abort", uploadCanceled, false);

					xhr.setRequestHeader("Content-Type", "multipart/form-data; boundary="+boundary);
					xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

					$.each(options.extraData, function(k, v) {
						body += boundaryVar(k, v, boundary);
					});

					body += boundaryVar('_upfile_data_type', 'dataurl', boundary);

					boundaryFile(options.dataName, file, boundary, body, xhr);
				}
			}

			/**
			 * onFilesSelected 
			 * 
			 * @param event $event 
			 * @return void
			 */
			function onFilesSelected(event) {
				event.preventDefault();
				event.stopPropagation();
				addFiles(event.target.files);

				return options.onFilesSelected();
			}

			/**
			 * addFiles add files to the file list
			 * 
			 * @param files $files 
			 * @return void
			 */
			function addFiles(files) {
				if (fileApiSupported()) {
					// preparing thumbnails div
					prepareThumbnails();

					var img = null;
					var reader = null;

					var dropZoneElement = options.dropZone;
					for (var i=0; i < files.length; i++) {
						if (options.maxFileSize > 0 && file.size > options.maxFileSize) {
							return options.error('file too big');
						}

						if (options.allowedExtension) {
							var types = options.allowedExtension.split(','),
							support = false;
							
							for (var j = 0; j < types.length; j++) {
								if (files[i].type.match($.trim(types[j]))) {
									support = true;
									break;
								}
							}
							if (!support) return options.error('file '+ files[i].type+' not support');
						}

						if (options.showThumbnails == true) {
							reader = new FileReader();
							reader.onloadend = function (evt) {
								var thumb = new Image();
								thumb.src = evt.target.result;
								if (options.thumbnails.width > 0) {
									thumb.width = options.thumbnails.width;
								}
								if (options.thumbnails.height > 0) {
									thumb.height = options.thumbnails.height;
								}
								options.thumbnails.div.append(thumb);

							};
							reader.readAsDataURL(files[i]);
						}

						uploadFile(files[i]);
					}
			   } else {
				   alert('files api not supported');
			   }
			}

			/**
			 * prepare thumbnails div
			 * 
			 * @return void
			 */
			function prepareThumbnails() {
				if (options.thumbnails) {
					if (typeof options.thumbnails != 'object') {
						var tmpDiv = null;
						if (typeof options.thumbnails == 'string') {
							tmpDiv = $(options.thumbnails);
						}
						options.thumbnails = { div: tmpDiv, width: null, height: null };
					}

					if (typeof options.thumbnails.div == 'string') {
						options.thumbnails.div = $(options.thumbnails.div);
					}

					if (typeof options.thumbnails == 'object' && options.thumbnails.div == undefined) {
						options.thumbnails = { div: options.thumbnails, width: null, height: null };
					}

					if (options.thumbnails.div == null) {
						options.thumbnails.div = $('<div class="fileUploadThumbnails" />');
						options.dropZone.after(options.thumbnails.div);
					}
				}
			}



			// =============================== 
			// main process
			// =============================== 

			// Dropzone management
			if (options.dropZone != null) {
				options.dropZone.on('dragleave', onDragLeave);
				options.dropZone.on('dragenter', onDragEnter);
				options.dropZone.on('dragover', onDragOver);
				options.dropZone.on('drop', onDrop);
			}

			if (options.fileField != null) {
				if (typeof options.fileField == 'string') {
					options.fileField = $(options.fileField);
				}
				options.fileField.on('change', onFilesSelected);
			}

			return this;
		};
	})(jQuery);
}
;(function($) {
    $.fn.mntips = function(rows, opts) {
        opts = $.extend({
			// 'top', 'bottom', 'right', 'left', 'center'
			position: ['bottom', 'center'], 
			offset: [0, 0],
			relative: false,

            tipClass: 'mntip',

            rowspec: null,      // ['id',...]

            clickRow: function(row, evt) {return false;}
        }, opts||{});

	    /* calculate tip position relative to the trigger */  	
	    function getPosition(trigger, tip) {
		    // get origin top/left position 
		    var conf = opts,
            top = conf.relative ? trigger.position().top : trigger.offset().top, 
			left = conf.relative ? trigger.position().left : trigger.offset().left,
			pos = conf.position[0];

		    top  -= tip.outerHeight() - conf.offset[0];
		    left += trigger.outerWidth() + conf.offset[1];
		    
		    // iPad position fix
		    if (/iPad/i.test(navigator.userAgent)) {
			    top -= $(window).scrollTop();
		    }
		    
		    // adjust Y		
		    var height = tip.outerHeight() + trigger.outerHeight();
		    if (pos == 'center') 	{ top += height / 2; }
		    if (pos == 'bottom') 	{ top += height; }
		    
		    
		    // adjust X
		    pos = conf.position[1]; 	
		    var width = tip.outerWidth() + trigger.outerWidth();
		    if (pos == 'center') 	{ left -= width / 2; }
		    if (pos == 'left')   	{ left -= width; }	 
		    
		    return {top: top, left: left};
	    }		

        $(this).each(function() {
            var me = $(this),
            tipdiv = $('<ul class="'+opts.tipClass+'">');

            if (me.data('mntip')) {
                 me.data('mntip').remove();
            }
            
            $.each(rows, function(i, row) {
                var item = $("<li>").appendTo(tipdiv).click(function(evt) {
                    tipdiv.hide();
                    opts.clickRow(row, evt);
                });
                if (bmoon.utl.type(opts.rowspec) == 'Array') {
                    $.each(opts.rowspec, function(i, col) {
                        $("<span class='tipscol"+i+"'>"+row[col]+"</span>").appendTo(item);
                    });
                } else {
                    $('<span>'+row+'</span>').appendTo(item);
                }
            });

            tipdiv.appendTo(document.body).hide();

            var pos = getPosition(me, tipdiv);

            tipdiv.css({position: 'absolute', left: pos.left, top: pos.top}).show();

            me.data('mntip', tipdiv);
        });
    };
})(jQuery);
; var bmoon = bmoon || {};
bmoon.admin = {
    version: '1.0',

    init: function() {
        var o = bmoon.admin;
        if (o.inited) return o;

        o.e_content = $('#bd-content');
        
        o.inited = true;
        return o;
    },
    
    onready: function() {
        var o = bmoon.admin.init();

        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.admin.init();

    },

    inTip: function(obj, opts) {
        var o = bmoon.admin.init();

        var me = $(obj),
        v = me.val();

        o.e_current_tip = me;

        if (!me.inputTipID) {
            me.inputTipID = setInterval(function() {
                if (me.val() != v) {
                    v = me.val();
                    o.getClassTip(opts.clickRow);
                }
            }, 500);
        }
    },

    outTip: function() {
        var o = bmoon.admin.init();

        var me = o.e_current_tip;

        me.inputTipID && clearInterval(me.inputTipID);
        me.inputTipID = 0;
    },

    getClassTip: function(clickRow) {
        var o = bmoon.admin.init();

        var me = o.e_current_tip,
        v = me.val();

        $.getJSON('/json/paper/matchtitle', {title: v}, function(data) {
            if (data.success == 1 && bmoon.utl.type(data.titles) == 'Array') {
                me.mntips(data.titles, {
                    rowspec: ['title'],
                    clickRow: clickRow
                });
            }
        });
    }
};

$(document).ready(bmoon.admin.onready);
/**
* jQuery Noty Plugin v1.1.1
* Authors: Nedim Arabacı (http://ned.im), Muhittin Özer (http://muhittinozer.com)
*
* Examples and Documentation - http://needim.github.com/noty/
*
* Licensed under the MIT licenses:
* http://www.opensource.org/licenses/mit-license.php
*
**/
(function($) {
	$.noty = function(options, customContainer) {

		var base = this;
		var $noty = null;
		var isCustom = false;

		base.init = function(options) {
			base.options = $.extend({}, $.noty.defaultOptions, options);
			base.options.type = base.options.cssPrefix+base.options.type;
			base.options.id = base.options.type+'_'+new Date().getTime();
			base.options.layout = base.options.cssPrefix+'layout_'+base.options.layout;

			if (base.options.custom.container) customContainer = base.options.custom.container;
			isCustom = ($.type(customContainer) === 'object') ? true : false;

			return base.addQueue();
		};

		// Push notification to queue
		base.addQueue = function() {
			var isGrowl = ($.inArray(base.options.layout, $.noty.growls) == -1) ? false : true;
	  	if (!isGrowl) (base.options.force) ? $.noty.queue.unshift({options: base.options}) : $.noty.queue.push({options: base.options});
	  	return base.render(isGrowl);
		};

		// Render the noty
		base.render = function(isGrowl) {

			// Layout spesific container settings
			var container = (isCustom) ? customContainer.addClass(base.options.theme+' '+base.options.layout+' noty_custom_container') : $('body');
	  	if (isGrowl) {
	  		if ($('ul.noty_cont.' + base.options.layout).length == 0)
	  			container.prepend($('<ul/>').addClass('noty_cont ' + base.options.layout));
	  		container = $('ul.noty_cont.' + base.options.layout);
	  	} else {
	  		if ($.noty.available) {
					var fromQueue = $.noty.queue.shift(); // Get noty from queue
					if ($.type(fromQueue) === 'object') {
						$.noty.available = false;
						base.options = fromQueue.options;
					} else {
						$.noty.available = true; // Queue is over
						return base.options.id;
					}
	  		} else {
	  			return base.options.id;
	  		}
	  	}
	  	base.container = container;

	  	// Generating noty bar
	  	base.bar = $('<div class="noty_bar"/>').attr('id', base.options.id).addClass(base.options.theme+' '+base.options.layout+' '+base.options.type);
	  	$noty = base.bar;
	  	$noty.append(base.options.template).find('.noty_text').html(base.options.text);
	  	$noty.data('noty_options', base.options);

	  	// Close button display
	  	(base.options.closeButton) ? $noty.addClass('noty_closable').find('.noty_close').show() : $noty.find('.noty_close').remove();

	  	// Bind close event to button
	  	$noty.find('.noty_close').bind('click', function() { $noty.trigger('noty.close'); });

	  	// If we have a button we must disable closeOnSelfClick and closeOnSelfOver option
	  	if (base.options.buttons) base.options.closeOnSelfClick = base.options.closeOnSelfOver = false;
	  	// Close on self click
	  	if (base.options.closeOnSelfClick) $noty.bind('click', function() { $noty.trigger('noty.close'); }).css('cursor', 'pointer');
	  	// Close on self mouseover
	  	if (base.options.closeOnSelfOver) $noty.bind('mouseover', function() { $noty.trigger('noty.close'); }).css('cursor', 'pointer');

	  	// Set buttons if available
	  	if (base.options.buttons) {
				$buttons = $('<div/>').addClass('noty_buttons');
				$noty.find('.noty_message').append($buttons);
				$.each(base.options.buttons, function(i, button) {
					bclass = (button.type) ? button.type : 'gray';
					$button = $('<button/>').addClass(bclass).html(button.text).appendTo($noty.find('.noty_buttons'))
					.bind('click', function() {
						if ($.isFunction(button.click)) {
							button.click.call($button, $noty);
						}
					});
				});
			}

	  	return base.show(isGrowl);
		};

		base.show = function(isGrowl) {

			// is Modal?
			if (base.options.modal) $('<div/>').addClass('noty_modal').addClass(base.options.theme).prependTo($('body')).fadeIn('fast');

			$noty.close = function() { return this.trigger('noty.close'); };

			// Prepend noty to container
			(isGrowl) ? base.container.prepend($('<li/>').append($noty)) : base.container.prepend($noty);

	  	// topCenter and center specific options
	  	if (base.options.layout == 'noty_layout_topCenter' || base.options.layout == 'noty_layout_center') {
				$.noty.reCenter($noty);
			}

	  	$noty.bind('noty.setText', function(event, text) {
	  		$noty.find('.noty_text').html(text); $.noty.reCenter($noty);
	  	});

	  	$noty.bind('noty.getId', function(event) {
	  		return $noty.data('noty_options').id;
	  	});

	  	// Bind close event
	  	$noty.one('noty.close', function(event) {
				var options = $noty.data('noty_options');

				// Modal Cleaning
				if (options.modal) $('.noty_modal').fadeOut('fast', function() { $(this).remove(); });

				$noty.clearQueue().stop().animate(
						$noty.data('noty_options').animateClose,
						$noty.data('noty_options').speed,
						$noty.data('noty_options').easing,
						$noty.data('noty_options').onClose)
				.promise().done(function() {

					// Layout spesific cleaning
					if ($.inArray($noty.data('noty_options').layout, $.noty.growls) > -1) {
						$noty.parent().remove();
					} else {
						$noty.remove();

						// queue render
						$.noty.available = true;
						base.render(false);
					}

				});
			});

	  	// Start the show
	  	$noty.animate(base.options.animateOpen, base.options.speed, base.options.easing, base.options.onShow);

	  	// If noty is have a timeout option
	  	if (base.options.timeout) $noty.delay(base.options.timeout).promise().done(function() { $noty.trigger('noty.close'); });
			return base.options.id;
		};

		// Run initializer
		return base.init(options);
	};

	// API
	$.noty.get = function(id) { return $('#'+id); };
	$.noty.close = function(id) {
		$.noty.get(id).trigger('noty.close');
	};
	$.noty.setText = function(id, text) {
		$.noty.get(id).trigger('noty.setText', text);
	};
	$.noty.closeAll = function() {
		$.noty.clearQueue();
		$('.noty_bar').trigger('noty.close');
	};
	$.noty.reCenter = function(noty) {
		noty.css({'left': ($(window).width() - noty.outerWidth()) / 2 + 'px'});
	};
	$.noty.clearQueue = function() {
		$.noty.queue = [];
	};

	$.noty.queue = [];
	$.noty.growls = ['noty_layout_topLeft', 'noty_layout_topRight', 'noty_layout_bottomLeft', 'noty_layout_bottomRight'];
	$.noty.available = true;
	$.noty.defaultOptions = {
		layout: 'top',
		theme: 'noty_theme_default',
		animateOpen: {height: 'toggle'},
		animateClose: {height: 'toggle'},
		easing: 'swing',
		text: '',
		type: 'alert',
		speed: 500,
		timeout: 5000,
		closeButton: false,
		closeOnSelfClick: true,
		closeOnSelfOver: false,
		force: false,
		onShow: false,
		onClose: false,
		buttons: false,
		modal: false,
		template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
		cssPrefix: 'noty_',
		custom: {
			container: null
		}
	};

	$.fn.noty = function(options) {
		return this.each(function() {
			 (new $.noty(options, $(this)));
		});
	};

})(jQuery);

//Helper
function noty(options) {
	return jQuery.noty(options); // returns an id
}; var bmoon = bmoon || {};
bmoon.admpapernew = {
    version: '1.0',

    init: function() {
        var o = bmoon.admpapernew;
        if (o.inited) return o;

        o.e_page_class = $('#page-class');
        o.e_paper_info = $('#paper-info');
        o.e_content = $('#content');
        o.e_submit = $('#submit');

        o.e_paper_info.data('_postData', {});
        
        o.inited = true;
        return o;
    },
    
    onready: function() {
        var o = bmoon.admpapernew.init();

        o.e_content.markItUp(myMarkdownSettings);
        o.e_content.uploader({
            fileField: '#mkd-input-file',
            url: '/json/zero/image',
            dataName: 'upfile',
            extraData: {_op: 'add'},
            allowedExtension: 'jpeg, bmp, png, gif',
	        afterUpload: function (data) {
                data = jQuery.parseJSON(data);
                if (data.success == 1) {
                    o.imagenum++;
                    var s = '\n' +
                        '[![图片' + o.imagenum+ '](' + data.imageurl_zoom + ' "' + data.imagename+ '")]'+
                        '(' + data.imageurl + ' "' + '点击查看原图")' +
                        '\n';
                    $.markItUp({replaceWith: s});
                } else alert(data.errmsg || '上传图片失败');
	        },
            error: function(msg) {
                alert(msg);
            }
        });
        
        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.admpapernew.init();

        o.e_submit.click(o.savePaper);

        o.e_page_class.focus(o.inTip);
        o.e_page_class.blur(bmoon.admin.outTip);

        $('input, textarea', o.e_paper_info).change(function() {
            o.e_paper_info.data('_postData')[$(this).attr('name')] = $(this).val();
        });
    },

    savePaper: function() {
        var o = bmoon.admpapernew.init();

        if (!$('.VAL_NEWPAPER').inputval()) return;

        var me = $(this),
        p = me.parent(),
        pdata = o.e_paper_info.data('_postData');

        pdata._op = 'add';

        $('.vres', p).remove();
        p.removeClass('success').removeClass('error').addClass('loading');

        $.post('/json/admin/paper', pdata, function(data) {
            p.removeClass('loading');
            if (data.success == 1) {
                p.addClass('success');
                noty({text:'文章添加成功', type: 'success', theme: 'noty_theme_mitgux'});
            } else {
                p.addClass('error');
                noty({text: data.errmsg, type: 'error', theme: 'noty_theme_mitgux'});
            }
        }, 'json');
    },

    inTip: function() {
        var o = bmoon.admpapernew.init();

        var me = $(this);

        bmoon.admin.inTip(me, {
            clickRow: function (row) {
                bmoon.admin.outTip();
                me.val(row.title);
                o.e_paper_info.data('_postData').pid = row.id;
                $('input[name="keyword"]').focus();
            }
        });
    }
};

$(document).ready(bmoon.admpapernew.onready);
