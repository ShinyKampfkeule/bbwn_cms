/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - [year] Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */
jQuery(document).ready((function(s){jQuery(".js--start").on("click",(function(e){jQuery(".js--start").addClass("disabled").attr("disabled","disabled"),jQuery(".progress").removeClass("hidden");var a=parseInt(s(this).attr("data-timeout"));!function s(e){jQuery.ajax({url:"index.php?option=com_pwtacl&task=diagnostics.runDiagnostics&step="+e,dataType:"json",success:function(t){var r=t.data.total,n=t.data.items,d="",l=".step"+e;if(n)for(var o in n){for(var i in n[o])for(var c in n[o][i]){var u=n[o][i][c];for(var p in d+="<tr>",d+='<td><span class="typeofchange badge badge-'+u.label+" bg-"+u.label+'">'+u.action+"</span></td>",d+='<td><span class="icon-space '+u.icon+'"></span>'+u.object+"</td>",d+="<td>"+u.title+"<br><small>"+u.name+"</small></td>",d+="<td>",u.changes){var m=u.changes[p];m.old&&(d+='<div class="btn-group btn-group-vertical"><span class="btn btn-small btn-sm bg-white">'+p+'</span><span class="btn btn-small btn-sm btn-danger">'+m.old+'</span><span class="btn btn-small btn-sm btn-success">'+m.new+"</span></div>")}d+="</td>",d+="<td>"+u.id+"</td>",d+="</tr>"}jQuery(l+" table").removeClass("hidden"),jQuery(l+" tbody").html(d)}jQuery(".progress .bar").attr("style","width:"+100/14*e+"%"),jQuery(l+" .js-step-heading").removeClass("muted").addClass("text-success"),jQuery(l+" .js-step-done").removeClass("hidden"),jQuery(l+" .js-results-alert").removeClass("hidden"),r&&(jQuery(l+" .js-assets-fixed").removeClass("hidden"),jQuery(l+" .js-assets-fixed-number").html(r)),++e<=14?setTimeout((function(){s(e)}),a):(jQuery(".completed").removeClass("hidden"),jQuery(".progress").removeClass("active").removeClass("progress-striped"),jQuery(".quickscan-issues").addClass("hidden"),jQuery(".quickscan-noissues").removeClass("hidden"))},error:function(s){console.log("error"+s)}})}(1)}))}));