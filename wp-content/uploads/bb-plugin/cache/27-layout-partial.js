/*!
 * Bowser - a browser detector
 * https://github.com/ded/bowser
 * MIT License | (c) Dustin Diaz 2015
 */!function(name,definition){if(typeof module!='undefined'&&module.exports)module.exports=definition()
else if(typeof define=='function'&&define.amd)define(name,definition)
else this[name]=definition()}('bowser',function(){var t=true
function detect(ua){function getFirstMatch(regex){var match=ua.match(regex);return(match&&match.length>1&&match[1])||'';}
function getSecondMatch(regex){var match=ua.match(regex);return(match&&match.length>1&&match[2])||'';}
var iosdevice=getFirstMatch(/(ipod|iphone|ipad)/i).toLowerCase(),likeAndroid=/like android/i.test(ua),android=!likeAndroid&&/android/i.test(ua),nexusMobile=/nexus\s*[0-6]\s*/i.test(ua),nexusTablet=!nexusMobile&&/nexus\s*[0-9]+/i.test(ua),chromeos=/CrOS/.test(ua),silk=/silk/i.test(ua),sailfish=/sailfish/i.test(ua),tizen=/tizen/i.test(ua),webos=/(web|hpw)os/i.test(ua),windowsphone=/windows phone/i.test(ua),windows=!windowsphone&&/windows/i.test(ua),mac=!iosdevice&&!silk&&/macintosh/i.test(ua),linux=!android&&!sailfish&&!tizen&&!webos&&/linux/i.test(ua),edgeVersion=getFirstMatch(/edge\/(\d+(\.\d+)?)/i),versionIdentifier=getFirstMatch(/version\/(\d+(\.\d+)?)/i),tablet=/tablet/i.test(ua),mobile=!tablet&&/[^-]mobi/i.test(ua),xbox=/xbox/i.test(ua),result
if(/opera|opr|opios/i.test(ua)){result={name:'Opera',opera:t,version:versionIdentifier||getFirstMatch(/(?:opera|opr|opios)[\s\/](\d+(\.\d+)?)/i)}}
else if(/coast/i.test(ua)){result={name:'Opera Coast',coast:t,version:versionIdentifier||getFirstMatch(/(?:coast)[\s\/](\d+(\.\d+)?)/i)}}
else if(/yabrowser/i.test(ua)){result={name:'Yandex Browser',yandexbrowser:t,version:versionIdentifier||getFirstMatch(/(?:yabrowser)[\s\/](\d+(\.\d+)?)/i)}}
else if(/ucbrowser/i.test(ua)){result={name:'UC Browser',ucbrowser:t,version:getFirstMatch(/(?:ucbrowser)[\s\/](\d+(?:\.\d+)+)/i)}}
else if(/mxios/i.test(ua)){result={name:'Maxthon',maxthon:t,version:getFirstMatch(/(?:mxios)[\s\/](\d+(?:\.\d+)+)/i)}}
else if(/epiphany/i.test(ua)){result={name:'Epiphany',epiphany:t,version:getFirstMatch(/(?:epiphany)[\s\/](\d+(?:\.\d+)+)/i)}}
else if(/puffin/i.test(ua)){result={name:'Puffin',puffin:t,version:getFirstMatch(/(?:puffin)[\s\/](\d+(?:\.\d+)?)/i)}}
else if(/sleipnir/i.test(ua)){result={name:'Sleipnir',sleipnir:t,version:getFirstMatch(/(?:sleipnir)[\s\/](\d+(?:\.\d+)+)/i)}}
else if(/k-meleon/i.test(ua)){result={name:'K-Meleon',kMeleon:t,version:getFirstMatch(/(?:k-meleon)[\s\/](\d+(?:\.\d+)+)/i)}}
else if(windowsphone){result={name:'Windows Phone',windowsphone:t}
if(edgeVersion){result.msedge=t
result.version=edgeVersion}
else{result.msie=t
result.version=getFirstMatch(/iemobile\/(\d+(\.\d+)?)/i)}}
else if(/msie|trident/i.test(ua)){result={name:'Internet Explorer',msie:t,version:getFirstMatch(/(?:msie |rv:)(\d+(\.\d+)?)/i)}}else if(chromeos){result={name:'Chrome',chromeos:t,chromeBook:t,chrome:t,version:getFirstMatch(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)}}else if(/chrome.+? edge/i.test(ua)){result={name:'Microsoft Edge',msedge:t,version:edgeVersion}}
else if(/vivaldi/i.test(ua)){result={name:'Vivaldi',vivaldi:t,version:getFirstMatch(/vivaldi\/(\d+(\.\d+)?)/i)||versionIdentifier}}
else if(sailfish){result={name:'Sailfish',sailfish:t,version:getFirstMatch(/sailfish\s?browser\/(\d+(\.\d+)?)/i)}}
else if(/seamonkey\//i.test(ua)){result={name:'SeaMonkey',seamonkey:t,version:getFirstMatch(/seamonkey\/(\d+(\.\d+)?)/i)}}
else if(/firefox|iceweasel|fxios/i.test(ua)){result={name:'Firefox',firefox:t,version:getFirstMatch(/(?:firefox|iceweasel|fxios)[ \/](\d+(\.\d+)?)/i)}
if(/\((mobile|tablet);[^\)]*rv:[\d\.]+\)/i.test(ua)){result.firefoxos=t}}
else if(silk){result={name:'Amazon Silk',silk:t,version:getFirstMatch(/silk\/(\d+(\.\d+)?)/i)}}
else if(/phantom/i.test(ua)){result={name:'PhantomJS',phantom:t,version:getFirstMatch(/phantomjs\/(\d+(\.\d+)?)/i)}}
else if(/slimerjs/i.test(ua)){result={name:'SlimerJS',slimer:t,version:getFirstMatch(/slimerjs\/(\d+(\.\d+)?)/i)}}
else if(/blackberry|\bbb\d+/i.test(ua)||/rim\stablet/i.test(ua)){result={name:'BlackBerry',blackberry:t,version:versionIdentifier||getFirstMatch(/blackberry[\d]+\/(\d+(\.\d+)?)/i)}}
else if(webos){result={name:'WebOS',webos:t,version:versionIdentifier||getFirstMatch(/w(?:eb)?osbrowser\/(\d+(\.\d+)?)/i)};if(/touchpad\//i.test(ua)){result.touchpad=t;}}
else if(/bada/i.test(ua)){result={name:'Bada',bada:t,version:getFirstMatch(/dolfin\/(\d+(\.\d+)?)/i)};}
else if(tizen){result={name:'Tizen',tizen:t,version:getFirstMatch(/(?:tizen\s?)?browser\/(\d+(\.\d+)?)/i)||versionIdentifier};}
else if(/qupzilla/i.test(ua)){result={name:'QupZilla',qupzilla:t,version:getFirstMatch(/(?:qupzilla)[\s\/](\d+(?:\.\d+)+)/i)||versionIdentifier}}
else if(/chromium/i.test(ua)){result={name:'Chromium',chromium:t,version:getFirstMatch(/(?:chromium)[\s\/](\d+(?:\.\d+)?)/i)||versionIdentifier}}
else if(/chrome|crios|crmo/i.test(ua)){result={name:'Chrome',chrome:t,version:getFirstMatch(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)}}
else if(android){result={name:'Android',version:versionIdentifier}}
else if(/safari|applewebkit/i.test(ua)){result={name:'Safari',safari:t}
if(versionIdentifier){result.version=versionIdentifier}}
else if(iosdevice){result={name:iosdevice=='iphone'?'iPhone':iosdevice=='ipad'?'iPad':'iPod'}
if(versionIdentifier){result.version=versionIdentifier}}
else if(/googlebot/i.test(ua)){result={name:'Googlebot',googlebot:t,version:getFirstMatch(/googlebot\/(\d+(\.\d+))/i)||versionIdentifier}}
else{result={name:getFirstMatch(/^(.*)\/(.*) /),version:getSecondMatch(/^(.*)\/(.*) /)};}
if(!result.msedge&&/(apple)?webkit/i.test(ua)){if(/(apple)?webkit\/537\.36/i.test(ua)){result.name=result.name||"Blink"
result.blink=t}else{result.name=result.name||"Webkit"
result.webkit=t}
if(!result.version&&versionIdentifier){result.version=versionIdentifier}}else if(!result.opera&&/gecko\//i.test(ua)){result.name=result.name||"Gecko"
result.gecko=t
result.version=result.version||getFirstMatch(/gecko\/(\d+(\.\d+)?)/i)}
if(!result.msedge&&(android||result.silk)){result.android=t}else if(iosdevice){result[iosdevice]=t
result.ios=t}else if(mac){result.mac=t}else if(xbox){result.xbox=t}else if(windows){result.windows=t}else if(linux){result.linux=t}
var osVersion='';if(result.windowsphone){osVersion=getFirstMatch(/windows phone (?:os)?\s?(\d+(\.\d+)*)/i);}else if(iosdevice){osVersion=getFirstMatch(/os (\d+([_\s]\d+)*) like mac os x/i);osVersion=osVersion.replace(/[_\s]/g,'.');}else if(android){osVersion=getFirstMatch(/android[ \/-](\d+(\.\d+)*)/i);}else if(result.webos){osVersion=getFirstMatch(/(?:web|hpw)os\/(\d+(\.\d+)*)/i);}else if(result.blackberry){osVersion=getFirstMatch(/rim\stablet\sos\s(\d+(\.\d+)*)/i);}else if(result.bada){osVersion=getFirstMatch(/bada\/(\d+(\.\d+)*)/i);}else if(result.tizen){osVersion=getFirstMatch(/tizen[\/\s](\d+(\.\d+)*)/i);}
if(osVersion){result.osversion=osVersion;}
var osMajorVersion=osVersion.split('.')[0];if(tablet||nexusTablet||iosdevice=='ipad'||(android&&(osMajorVersion==3||(osMajorVersion>=4&&!mobile)))||result.silk){result.tablet=t}else if(mobile||iosdevice=='iphone'||iosdevice=='ipod'||android||nexusMobile||result.blackberry||result.webos||result.bada){result.mobile=t}
if(result.msedge||(result.msie&&result.version>=10)||(result.yandexbrowser&&result.version>=15)||(result.vivaldi&&result.version>=1.0)||(result.chrome&&result.version>=20)||(result.firefox&&result.version>=20.0)||(result.safari&&result.version>=6)||(result.opera&&result.version>=10.0)||(result.ios&&result.osversion&&result.osversion.split(".")[0]>=6)||(result.blackberry&&result.version>=10.1)||(result.chromium&&result.version>=20)){result.a=t;}
else if((result.msie&&result.version<10)||(result.chrome&&result.version<20)||(result.firefox&&result.version<20.0)||(result.safari&&result.version<6)||(result.opera&&result.version<10.0)||(result.ios&&result.osversion&&result.osversion.split(".")[0]<6)||(result.chromium&&result.version<20)){result.c=t}else result.x=t
return result}
var bowser=detect(typeof navigator!=='undefined'?navigator.userAgent:'')
bowser.test=function(browserList){for(var i=0;i<browserList.length;++i){var browserItem=browserList[i];if(typeof browserItem==='string'){if(browserItem in bowser){return true;}}}
return false;}
function getVersionPrecision(version){return version.split(".").length;}
function map(arr,iterator){var result=[],i;if(Array.prototype.map){return Array.prototype.map.call(arr,iterator);}
for(i=0;i<arr.length;i++){result.push(iterator(arr[i]));}
return result;}
function compareVersions(versions){var precision=Math.max(getVersionPrecision(versions[0]),getVersionPrecision(versions[1]));var chunks=map(versions,function(version){var delta=precision-getVersionPrecision(version);version=version+new Array(delta+1).join(".0");return map(version.split("."),function(chunk){return new Array(20-chunk.length).join("0")+chunk;}).reverse();});while(--precision>=0){if(chunks[0][precision]>chunks[1][precision]){return 1;}
else if(chunks[0][precision]===chunks[1][precision]){if(precision===0){return 0;}}
else{return-1;}}}
function isUnsupportedBrowser(minVersions,strictMode,ua){var _bowser=bowser;if(typeof strictMode==='string'){ua=strictMode;strictMode=void(0);}
if(strictMode===void(0)){strictMode=false;}
if(ua){_bowser=detect(ua);}
var version=""+_bowser.version;for(var browser in minVersions){if(minVersions.hasOwnProperty(browser)){if(_bowser[browser]){return compareVersions([version,minVersions[browser]])<0;}}}
return strictMode;}
function check(minVersions,strictMode,ua){return!isUnsupportedBrowser(minVersions,strictMode,ua);}
bowser.isUnsupportedBrowser=isUnsupportedBrowser;bowser.compareVersions=compareVersions;bowser.check=check;bowser._detect=detect;return bowser});(function($){UABBTrigger={triggerHook:function(hook,args){$('body').trigger('uabb-trigger.'+hook,args);},addHook:function(hook,callback){$('body').on('uabb-trigger.'+hook,callback);},removeHook:function(hook,callback){$('body').off('uabb-trigger.'+hook,callback);},};})(jQuery);jQuery(document).ready(function($){if(typeof bowser!=='undefined'&&bowser!==null){var uabb_browser=bowser.name,uabb_browser_v=bowser.version,uabb_browser_class=uabb_browser.replace(/\s+/g,'-').toLowerCase(),uabb_browser_v_class=uabb_browser_class+parseInt(uabb_browser_v);$('html').addClass(uabb_browser_class).addClass(uabb_browser_v_class);}
$('.uabb-row-separator').parents('html').css('overflow-x','hidden');});;(function($){if($(window).width()<=768&&$(window).width()>=481){$('.fl-node-5a8202163eb24 .pp-heading-separator, .fl-node-5a8202163eb24 .pp-heading').removeClass('pp-left');$('.fl-node-5a8202163eb24 .pp-heading-separator, .fl-node-5a8202163eb24 .pp-heading').addClass('pp-tablet-center');}
if($(window).width()<=480){$('.fl-node-5a8202163eb24 .pp-heading-separator, .fl-node-5a8202163eb24 .pp-heading').removeClass('pp-left');$('.fl-node-5a8202163eb24 .pp-heading-separator, .fl-node-5a8202163eb24 .pp-heading').addClass('pp-mobile-center');}})(jQuery);;(function($){$(function(){$('.fl-map').on('click',function(){$(this).find('iframe').css('pointer-events','auto');});});})(jQuery);;(function($){UABBSpacerGap=function(settings){this.settings=settings;this.node=settings.id;this.desktop_space=settings.desktop_space;this.medium_device=settings.medium_device;this.small_device=settings.small_device;this.nodeClass='.fl-node-'+settings.id;this._init();};UABBSpacerGap.prototype={settings:{},node:'',nodeClass:'',_init:function(){this._spacerGapHeightHandler();},_spacerGapHeightHandler:function(){var nodeClass=jQuery(this.nodeClass);var node_id=nodeClass.data('node');var node='.fl-node-'+node_id+' ';if(jQuery('body').hasClass('fl-builder-edit')&&this.desktop_space<30){jQuery('body').find(node).addClass("uabb-desktop-spacer-height-adjustment");}
if(jQuery('body').hasClass('fl-builder-edit')&&this.medium_device<30){jQuery('body').find(node).addClass('uabb-tab-spacer-height-adjustment');}
if(jQuery('body').hasClass('fl-builder-edit')&&this.small_device<30){jQuery('body').find(node).addClass('uabb-mobile-spacer-height-adjustment');}},};})(jQuery);jQuery(document).ready(function(){new UABBSpacerGap({id:'5b07fe7662a50',desktop_space:'',medium_device:'0',small_device:'0',});});;(function($){$('.fl-node-5a8202163ec8b .pp-infobox').on('mouseenter',function(){$('.fl-node-5a8202163ec8b .pp-icon-wrapper').addClass(' none');});$('.fl-node-5a8202163ec8b .pp-infobox').on('mouseleave',function(){$('.fl-node-5a8202163ec8b .pp-icon-wrapper').removeClass(' none');});})(jQuery);;(function($){window.onLoadFLReCaptcha=function(){var reCaptchaFields=$('.fl-grecaptcha'),widgetID;if(reCaptchaFields.length>0){reCaptchaFields.each(function(i){var self=$(this),attrWidget=self.attr('data-widgetid'),newID=$(this).attr('id')+'-'+i;if((typeof attrWidget!==typeof undefined&&attrWidget!==false)){return;}
else{self.attr('id',newID);widgetID=grecaptcha.render(newID,{sitekey:self.data('sitekey'),theme:self.data('theme'),size:self.data('validate'),callback:function(response){if(response!=''){self.attr('data-fl-grecaptcha-response',response);if('invisible'==self.data('validate')){self.closest('.fl-contact-form').find('.fl-button:is(a, button)').trigger('click');}}}});self.attr('data-widgetid',widgetID);}});}};FLBuilderContactForm=function(settings){this.settings=settings;this.nodeClass='.fl-node-'+settings.id;this._init();};FLBuilderContactForm.prototype={settings:{},nodeClass:'',_init:function(){$(this.nodeClass+' .fl-button').click($.proxy(this._submit,this));},_submit:function(e){var theForm=$(this.nodeClass+' .fl-contact-form'),submit=$(this.nodeClass+' .fl-button'),name=$(this.nodeClass+' .fl-name input'),email=$(this.nodeClass+' .fl-email input'),phone=$(this.nodeClass+' .fl-phone input'),subject=$(this.nodeClass+' .fl-subject input'),message=$(this.nodeClass+' .fl-message textarea'),termsCheckbox=$(this.nodeClass+' .fl-terms-checkbox input'),reCaptchaField=$(this.nodeClass+' .fl-grecaptcha'),reCaptchaValue=reCaptchaField.data('fl-grecaptcha-response'),ajaxData=null,ajaxurl=FLBuilderLayoutConfig.paths.wpAjaxUrl,email_regex=/\S+@\S+\.\S+/,isValid=true,postId=theForm.closest('.fl-builder-content').data('post-id'),layoutId=theForm.find('input[name=fl-layout-id]').val(),templateId=theForm.data('template-id'),templateNodeId=theForm.data('template-node-id'),nodeId=theForm.closest('.fl-module').data('node'),nonce=theForm.find('#fl-contact-form-nonce').val();e.preventDefault();if(submit.hasClass('fl-disabled')){return;}
if(name.length){if(name.val()===''){isValid=false;name.parent().addClass('fl-error');name.attr('aria-invalid',true);}
else if(name.parent().hasClass('fl-error')){name.parent().removeClass('fl-error');name.attr('aria-invalid',false);}}
if(email.length){if(email.val()===''||!email_regex.test(email.val())){isValid=false;email.parent().addClass('fl-error');email.attr('aria-invalid',true);}
else if(email.parent().hasClass('fl-error')){email.parent().removeClass('fl-error');email.attr('aria-invalid',false);}}
if(subject.length){if(subject.val()===''){isValid=false;subject.parent().addClass('fl-error');subject.attr('aria-invalid',true);}
else if(subject.parent().hasClass('fl-error')){subject.parent().removeClass('fl-error');subject.attr('aria-invalid',false);}}
if(phone.length){if(phone.val()===''){isValid=false;phone.parent().addClass('fl-error');phone.attr('aria-invalid',true);}
else if(phone.parent().hasClass('fl-error')){phone.parent().removeClass('fl-error');phone.attr('aria-invalid',false);}}
if(message.val()===''){isValid=false;message.parent().addClass('fl-error');message.attr('aria-invalid',true);}
else if(message.parent().hasClass('fl-error')){message.parent().removeClass('fl-error');message.attr('aria-invalid',false);}
if(termsCheckbox.length){if(!termsCheckbox.is(':checked')){isValid=false;termsCheckbox.attr('aria-invalid',true);termsCheckbox.closest('.fl-terms-checkbox').addClass('fl-error');}
else if(termsCheckbox.closest('.fl-input-group').hasClass('fl-error')){termsCheckbox.closest('.fl-input-group').removeClass('fl-error');termsCheckbox.attr('aria-invalid',false);}}
if(reCaptchaField.length>0&&isValid){if('undefined'===typeof reCaptchaValue||reCaptchaValue===false){if('normal'==reCaptchaField.data('validate')){reCaptchaField.parent().addClass('fl-error');}else if('invisible'==reCaptchaField.data('validate')){if('undefined'!==typeof reCaptchaField.data('action')){grecaptcha.execute(reCaptchaField.data('widgetid'),{action:reCaptchaField.data('action')});}
else{grecaptcha.execute(reCaptchaField.data('widgetid'));}}
isValid=false;}else{reCaptchaField.parent().removeClass('fl-error');}}
if(!isValid){return false;}
else{submit.addClass('fl-disabled');ajaxData={action:'fl_builder_email',name:name.val(),subject:subject.val(),email:email.val(),phone:phone.val(),message:message.val(),terms_checked:termsCheckbox.is(':checked')?'1':'0',post_id:postId,layout_id:layoutId,template_id:templateId,template_node_id:templateNodeId,node_id:nodeId,_wpnonce:nonce}
if(reCaptchaValue){ajaxData.recaptcha_response=reCaptchaValue;}
$.post(ajaxurl,ajaxData,$.proxy(this._submitComplete,this));}},_submitComplete:function(response){var urlField=$(this.nodeClass+' .fl-success-url'),noMessage=$(this.nodeClass+' .fl-success-none');response=response.data;if(typeof response.error!=='undefined'&&response.error===false){$(this.nodeClass+' .fl-send-error').fadeOut();if(urlField.length>0){window.location.href=urlField.val();}
else if(noMessage.length>0){noMessage.fadeIn();}
else{$(this.nodeClass+' .fl-contact-form').hide();$(this.nodeClass+' .fl-success-msg').fadeIn();}}
else{$(this.nodeClass+' .fl-button').removeClass('fl-disabled');if(typeof response.message!=='undefined'){$(this.nodeClass+' .fl-send-error').html(response.message);}
$(this.nodeClass+' .fl-send-error').fadeIn();if(typeof response.errorInfo!=='undefined'){console.log('Contact Form:',response.errorInfo);}
return false;}}};})(jQuery);(function($){$(function(){new FLBuilderContactForm({id:'fl6mxsn7upr9'});});})(jQuery);(function($){$('.fl-node-6ltd8phoy0km .pp-infobox').on('mouseenter',function(){$('.fl-node-6ltd8phoy0km .pp-icon-wrapper').addClass(' none');});$('.fl-node-6ltd8phoy0km .pp-infobox').on('mouseleave',function(){$('.fl-node-6ltd8phoy0km .pp-icon-wrapper').removeClass(' none');});})(jQuery);(function($){$('.fl-node-5a8202163ecc6 .pp-infobox').on('mouseenter',function(){$('.fl-node-5a8202163ecc6 .pp-icon-wrapper').addClass(' none');});$('.fl-node-5a8202163ecc6 .pp-infobox').on('mouseleave',function(){$('.fl-node-5a8202163ecc6 .pp-icon-wrapper').removeClass(' none');});})(jQuery);(function($){$('.fl-node-5a8202163ed3c .pp-infobox').on('mouseenter',function(){$('.fl-node-5a8202163ed3c .pp-icon-wrapper').addClass(' none');});$('.fl-node-5a8202163ed3c .pp-infobox').on('mouseleave',function(){$('.fl-node-5a8202163ed3c .pp-icon-wrapper').removeClass(' none');});})(jQuery);(function($){})(jQuery);