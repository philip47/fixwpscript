<?php //## NextScripts Medium Connection Class
$nxs_snapAvNts[] = array('code'=>'MD', 'lcode'=>'md', 'name'=>'Medium', 'type'=>'Blogs/Publishing Platforms', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Autopost to your profile or publications');

if (!class_exists("nxs_snapClassMD")) { class nxs_snapClassMD extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'MD', 'lcode'=>'md', 'name'=>'Medium', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'https://www.nextscripts.com/instructions/medium-auto-poster-setup-installation/');  
   
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'', 'accessToken'=>'', 'session'=>'', 'inclTags'=>'1', 'pubList'=>'', 'publ'=>'0', 'msgTFormat'=>'%TITLE%', 'msgFormat'=>"%FULLTEXT%\r\n\r\n%URL%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['accessToken']); }
  function doAuth() {  $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL;
    if (isset($_GET['acc'])) { $acc = sanitize_text_field($_GET['acc']); $options = $this->nt[$acc];
        if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode']){
          //if(stripos($nxs_snapSetPgURL, 'page=NextScripts_SNAP.php')===false) { $newURL = explode('?', $nxs_snapSetPgURL); $nxs_snapSetPgURL = $newURL[0]; }
          $url = 'https://medium.com/m/oauth/authorize?client_id='.nxs_gak($options['appKey']).'&scope=basicProfile,publishPost,listPublications&state=nxsmdauth-'. sanitize_text_field($_GET['auth']).'-'.$acc.'&response_type=code&redirect_uri='.urlencode($nxs_snapSetPgURL);
          echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.esc_url($url).'"</script>';
          die();
        }
        if ( isset($_GET['state']) && strlen($_GET['state'])>13 && substr($_GET['state'],0,12)=='nxsmdauth-md'){ $ii = explode('-',sanitize_text_field($_GET['state'])); $ii = $ii[2]; $options = $this->nt[$ii];
          $data = array('code'=>sanitize_text_field($_GET['code']), 'client_id'=>nxs_gak($options['appKey']),'client_secret'=>nxs_gas($options['appSec']),'grant_type'=>'authorization_code','redirect_uri'=>$nxs_snapSetPgURL);
          $hdrsArr = array(); $hdrsArr['Content-Type']='application/x-www-form-urlencoded'; $hdrsArr['Accept']='application/json'; $hdrsArr['Accept-Charset']='utf-8';
          $hdrsArr['Cache-Control']='max-age=0';  $hdrsArr['Referer']='';
          $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.54 Safari/537.36';
          $advSet = nxs_mkRemOptsArr($hdrsArr, '', $data); $rep = nxs_remote_post('https://api.medium.com/v1/tokens/', $advSet); prr($advSet); prr($rep);
          $bd = json_decode($rep['body'], true); if (!is_array($bd)) die('ERROR'); else prr($bd); $options['accessToken'] = $bd['access_token'];

          $hdrsArr['Authorization']='Bearer '.$options['accessToken'];
          $advSet = nxs_mkRemOptsArr($hdrsArr); $rep = nxs_remote_get('https://api.medium.com/v1/me', $advSet); prr($advSet); prr($rep);  if (is_nxs_error($rep)) {  $badOut = print_r($rep, true)." - ERROR -02-"; return $badOut; }
          $bd = json_decode($rep['body'], true); if (!is_array($bd) || !is_array($bd['data'])) die('ERROR'); else prr($bd); $bd = $bd['data'];
          $options['appAppUserID'] = $bd['id']; $options['appAppUserName'] = $bd['name'].' ('.$bd['username'].')';  $options['appAppUserURL'] = $bd['url'];

          $rep = nxs_remote_get('https://api.medium.com/v1/users/'.$options['appAppUserID'].'/publications', $advSet);  prr($advSet); prr($rep);
          $bd = json_decode($rep['body'], true); if (!is_array($bd) || !is_array($bd['data'])) die('ERROR'); else prr($bd); $bd = $bd['data'];   $pubList = array();
          foreach ($bd as $d) { $repX = nxs_remote_get('https://api.medium.com/v1/publications/'.$d['id'].'/contributors', $advSet);
            $bdX = json_decode($repX['body'], true); if (!is_array($bdX) || !is_array($bdX['data'])) die('ERROR'); else prr($bdX); $bdX = $bdX['data'];
            foreach ($bdX as $dX) { if ($dX['userId']==$options['appAppUserID']) { $pubList[] = array('id'=>$d['id'], 'name'=>$d['name']); break; }}
          } $options['pubList'] = $pubList;

          nxs_save_glbNtwrks($ntInfo['lcode'],$ii,$options,'*'); prr($options);
          echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$nxs_snapSetPgURL.'"</script>';  die();
        }
    }
  }
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; ?>

      <div class="container nxsNtSetContainer">
          <?php if (empty($options['session'])) $options['session'] = ''; //prr($conn); ?>
          <br/><div style="width:100%; font-size: 16px;"><strong><?php _e('Integration token', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong>
              <div style="font-size: 11px; margin: 0px;"><?php _e('Currently available way to connect to Medium', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
          </div>
          <label for="sesDiv<?php echo esc_attr($nt); ?><?php echo esc_attr($ii); ?>session">Integration token:</label>
          <input style="max-width:400px;" class="nxAccEdElem form-control" id="sesDiv<?php echo esc_attr($nt); ?><?php echo esc_attr($ii); ?>accessToken" name="<?php echo esc_attr($nt); ?>[<?php echo esc_attr($ii); ?>][accessToken]" value="<?php echo($options['accessToken']); ?>" /><br/>

      </div>

      <div class="container nxsNtSetContainer" style="padding: 15px; "> <div style="width:100%; font-size: 16px;"><strong><?php _e('Older/Legacy/Depreciated App Based Integration', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong>
              <div style="font-size: 11px; margin: 0px;"><?php _e('Please use this only if you still have Medium App. Leave empty if you are using "Integration token"', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
          </div>
          <div style="background-color: #cccccc; max-width: 300px; padding: 15px;">
          <?php $this->elemKeySecret($ii,'Medium Client ID','Medium Client Secret', $options['appKey'], $options['appSec']); ?>
          </div>
      </div>

    <br/><br/><div><b style="font-size: 15px;"><?php _e('Where to post:', 'social-networks-auto-poster-facebook-twitter-g'); ?></b><select name="md[<?php echo esc_attr($ii); ?>][publ]"><option <?php if (empty($options['publ'])) echo 'selected="selected"'; ?>  value="0">Profile</option>
      <?php if (!empty($options['pubList'])) { ?> <?php foreach ($options['pubList'] as $pb) {?> <option <?php if ((string)$pb['id']==(string)$options['publ']) echo 'selected="selected"'; ?> value="<?php echo $pb['id']; ?>">[Publication]&nbsp;<?php echo $pb['name']; ?></option> <?php }} ?></select>
    </div>
    <br/><?php $this->elemTitleFormat($ii,'Title Format','msgTFormat',$options['msgTFormat']); ?> <br/><?php $this->elemMsgFormat($ii,'Message Text Format','msgFormat',$options['msgFormat']); ?>
    <div style="margin-bottom: 20px;margin-top: 5px;"><input value="1" type="checkbox" name="<?php echo esc_attr($nt); ?>[<?php echo esc_attr($ii); ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> 
      <strong><?php _e('Post with tags', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong>  <?php _e('Tags from the blogpost will be auto-posted to '.$ntInfo['name'], 'social-networks-auto-poster-facebook-twitter-g'); ?>                                                               
    </div> <br/>
    <?php if(!empty($options['appKey'] && $options['appKey']!='x5g9a')) {
       if(isset($options['accessToken'])) {
      _e('Your '.$ntInfo['name'].' Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> User: <?php _e(apply_filters('format_to_edit', htmlentities($options['appAppUserName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>.
      <?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>            
      <a href="<?php echo $nxs_snapSetPgURL;?>&auth=<?php echo esc_attr($nt); ?>&acc=<?php echo esc_attr($ii); ?>">Authorize Your <?php echo $ntInfo['name']; ?> Account</a>            
      <?php if (!isset($options['appAppUserID']) || $options['appAppUserID']<1) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php } 
    } ?><br/><br/> <?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['accessToken']) || (!empty($pval['appSec']) && !empty($pval['appKey']))){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['inclTags'])) $options[$ii]['inclTags'] = trim($pval['inclTags']); else $options[$ii]['inclTags'] = 0;
          if (isset($pval['accessToken'])) $options[$ii]['accessToken'] = trim($pval['accessToken']); else $options[$ii]['accessToken'] = '';

           //## Getting user ID && List of Publications
           $argArr = ['ref'=>'https://medium.com',  'aj'=>true, 'extraHeaders'=>['Accept'=> 'application/json, text/javascript, */*; q=0.01','Content-Type'=>'application/json; charset=UTF-8', 'Authorization'=>'Bearer '.$options[$ii]['accessToken']]];
           $args = nxs_mkRmReqArgs($argArr); //prr($args, 'ARRRRGGGGGGGSSSSSSS');
           $rq = new WP_Http; $ret = $rq->request('https://api.medium.com/v1/me', $args); /* prr($ret, 'CALL RET'); */ if (is_nxs_error($ret)) return print_r($ret, true);
           if (!empty($ret['body'])) { $ui = json_decode($ret['body'], true); $options[$ii]['appAppUserID'] = $ui['data']['id'];
               if (!empty($options[$ii]['appAppUserID'])) $ret = $rq->request('https://api.medium.com/v1/users/'.$options[$ii]['appAppUserID'].'/publications', $args);
               if (!empty($ret['body'])) { $bd = json_decode($ret['body'], true);  $bd = $bd['data'];   $pubList = array();
                   foreach ($bd as $d) { $repX = $rq->request('https://api.medium.com/v1/publications/'.$d['id'].'/contributors', $args);
                       $bdX = json_decode($repX['body'], true); if (!is_array($bdX) || !is_array($bdX['data'])) die('ERROR'); else prr($bdX); $bdX = $bdX['data'];
                       foreach ($bdX as $dX) { if ($dX['userId']==$options[$ii]['appAppUserID']) { $pubList[] = array('id'=>$d['id'], 'name'=>$d['name']); break; }}
                   } $options[$ii]['pubList'] = $pubList;
               }
           }

          if (isset($pval['publ'])) $options[$ii]['publ'] = trim($pval['publ']); else $options[$ii]['publ'] = 0;
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
  //#### Show Post->Edit Meta Box Settings
  
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;
        
        $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); ?> 
        
        <?php $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);  $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); 
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  function showEdPostNTSettingsV4($ntOpt, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $ii = $ntOpt['ii']; //prr($ntOpt['postType']);                                                   
       if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
       $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
       $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;        
       //## Title and Message
       $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);        
       $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);
       // ## Select Image & URL       
       nxs_showURLToUseDlg($nt, $ii, $urlToUse); 
  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
    //if (!empty($pMeta['tgBoard'])) $optMt['tgBoard'] = $pMeta['tgBoard'];       
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ //prr($message); prr($options);
    if (!empty($postID)) { $postType = $options['postType'];
      if ($postType=='A') if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'medium');  
      if ($postType=='I') if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
      $message['imageURL'] = $imgURL;
    }
  }   
  
}}

if (!function_exists("nxs_doPublishToMD")) { function nxs_doPublishToMD($postID, $options){ 
    if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassMD(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}} 

?>