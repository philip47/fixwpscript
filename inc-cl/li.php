<?php    
//## NextScripts Facebook Connection Class
$nxs_snapAvNts[] = array('code'=>'LI', 'lcode'=>'li', 'name'=>'LinkedIn', 'type'=>'Social Networks', 'ptype'=>'B', 'status'=>'A', 'desc'=>'Post text, article, image or share a link to your profile, group, or company page. ');

if (!function_exists("nxs_ntp_time")) { function nxs_ntp_time($host='time.nist.gov') { $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); socket_connect($sock, $host, 123);   
  $msg = "\010" . str_repeat("\0", 47); socket_send($sock, $msg, strlen($msg), 0); socket_recv($sock, $recv, 48, MSG_WAITALL); socket_close($sock);
  $data = unpack('N12', $recv); $timestamp = sprintf('%u', $data[9]); $timestamp -= 2208988800;  return $timestamp;
}}

if (!class_exists("nxs_snapClassLI")) { class nxs_snapClassLI extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'LI', 'lcode'=>'li', 'name'=>'LinkedIn', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'https://www.nextscripts.com/setup-installation-linkedin-social-networks-auto-poster-wordpress/');
  var $defO = array('nName'=>'', 'do'=>'1', 'pgID'=>'', 'pgcID'=>'', 'appKey'=>'', 'appSec'=>'', 'uName'=>'', 'uPass'=>'',  'uPage'=>'', 'inclTags'=>1, 'msgFormat'=>"New post (%TITLE%) has been published on %SITENAME%", 'msgTFormat'=>"%TITLE%", 'msgCTFormat'=>"%TITLE%", 'msgCFormat'=>"%RAWTEXT%", 'msgATFormat'=>"", 'msgAFormat'=>"",  'imgSize'=>'original');
  //#### Update
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        if (!empty($ntOpts['liAPIKey'])) $ntOpts['apiToUse'] = (!empty($ntOpts['isV2']))?'liv2':'liv2'; else  if (!empty($ntOpts['ulName']) && !empty($ntOpts['uPass'])) $ntOpts['apiToUse'] = 'nx';  $ntOptsOut['apiToUse'] = $ntOpts['apiToUse'];
        if ($ntOptsOut['apiToUse']=='nx') { $ntOptsOut['uName'] = $ntOpts['ulName'];  $ntOptsOut['uPass'] = $ntOpts['uPass'];  } else { $ntOptsOut['appKey'] = $ntOpts['liAPIKey'];   $ntOptsOut['appSec'] = $ntOpts['liAPISec'];  
           $ntOptsOut['oAuthVerifier'] =  !empty($ntOpts['liOAuthVerifier'])?$ntOpts['liOAuthVerifier']:''; $ntOptsOut['accessToken'] = !empty($ntOpts['liAccessToken'])?$ntOpts['liAccessToken']:''; 
           $ntOptsOut['accessTokenSec'] = !empty($ntOpts['liAccessTokenSecret'])?$ntOpts['liAccessTokenSecret']:''; $ntOptsOut['oAuthToken'] =  !empty($ntOpts['liOAuthToken'])?$ntOpts['liOAuthToken']:''; 
           $ntOptsOut['oAuthTokenSecret'] = !empty($ntOpts['liOAuthTokenSecret'])?$ntOpts['liOAuthTokenSecret']:''; $ntOptsOut['accessTokenExp'] = !empty($ntOpts['liAccessTokenExp'])?$ntOpts['liAccessTokenExp']:''; 
           $ntOptsOut['liUserID'] = !empty($ntOpts['liUserID'])?$ntOpts['liUserID']:''; $ntOptsOut['liUserInfo'] = !empty($ntOpts['liUserInfo'])?$ntOpts['liUserInfo']:'';
        } $ntOptsOut['imgSize'] = !empty($ntOpts['imgSize'])?$ntOpts['imgSize']:''; $ntOptsOut['msgFormat'] = $ntOpts['liMsgFormat'];  $ntOptsOut['msgTFormat'] = $ntOpts['liMsgFormatT']; $ntOptsOut['msgAFormat'] = $ntOpts['liMsgAFrmt']; 
        $ntOptsOut['liUserInfo'] = !empty($ntOpts['liUserInfo'])?$ntOpts['liUserInfo']:''; $ntOptsOut['postType'] = $ntOpts['postType']; $ntOptsOut['grpID'] = !empty($ntOpts['grpID'])?$ntOpts['grpID']:''; $ntOptsOut['whToPost'] = 'PR';
        if ( substr($ntOpts['uPage'], 0, 4)=='http' ) { if (stripos($ntOpts['uPage'], 'groups')!==false && stripos($ntOpts['uPage'], 'gid=')!==false) { $lid = CutFromTo($ntOpts['uPage'].'&', 'gid=', '&'); } 
          else { $lid = $ntOpts['uPage']; if (strpos($lid, '?')!==false) $lid = substr($lid, 0, strpos($lid, '?')); if (substr($lid, -1)=='/') $lid = substr($lid, 0, -1);  $lid = substr(strrchr($lid, "/"), 1); }
        }  $ntOptsOut['pgcID'] = ''; $ntOptsOut['pggID'] = ''; if (!empty($lid)) { if (stripos($ntOpts['uPage'], 'groups')!==false) {  $ntOptsOut['whToPost'] = 'G';  $ntOptsOut['pggID'] = $lid; } else { $ntOptsOut['whToPost'] = 'C'; $ntOptsOut['pgcID'] = $lid; }}
        $ntOptsOut = nxs_arrMergeCheck($ntOptsOut, $this->defO); $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){$this->showGNewNTSettings($ii, $this->defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['accessToken']) || !empty($options['uPass']); }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL;     
    // V2 Auth Error
    if ( isset($_GET['page']) && $_GET['page']=='nxssnap' && !empty($_GET['error_description']) && isset($_GET['state']) && substr($_GET['state'], 0, 7) == 'nxs-li-'){
        $this->showAuthTop();  $ii = sanitize_text_field(str_replace('nxs-li-','',$_GET['state'])); $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $isNew = false;
        $nto = $this->nt[$ii];
        echo '----=={ oAuth 2.0 LinkedIn ERROR }==----<br/><br/><div style="color:red;">'; 
        prr(urldecode($_GET['error_description']));
        
        if (stripos($_GET['error_description'],'rw_organization_admin')!==false) echo '<br/>It looks like Marketing API is not approved for your LinkedIn API V2 Application. Please use "Authorize without Marketing API".<br/>';        
        
        $gGet = $_GET; unset($gGet['code']); unset($gGet['state']); unset($gGet['error_description']); unset($gGet['post_type']); unset($gGet['activated']); unset($gGet['stylesheet']);  $sturl = explode('?',$nxs_snapSetPgURL); $nxs_snapSetPgURL = $sturl[0].((!empty($gGet))?'?'.http_build_query($gGet):'');       
        
        ?>
        <a href="#" onclick="var url = 'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=<?php echo nxs_gak($nto['appKey']);?>&scope=r_liteprofile+r_emailaddress+w_member_social&state=nxs-li-<?php echo esc_attr($ii); ?>&redirect_uri=<?php echo trim(urlencode($nxs_snapSetPgURL));?>'; nxs_svSetAdv('<?php echo esc_attr($nt); ?>', '<?php echo esc_attr($ii); ?>', '<?php echo $isNew?'dom'.$ntU.$ii.'Div':'nxsAllAccntsDiv'; ?>','nxs<?php echo $ntU; ?>MsgDiv<?php echo esc_attr($ii); ?>',url,'1'); return false;">Authorize Your LinkedIn Account (<b>without</b> Marketing API)</a>
        <?php
        die('</div></div></div>');
    }
    // V2 Auth
    if ( isset($_GET['code']) && $_GET['code']!='' && isset($_GET['state']) && substr($_GET['state'], 0, 7) == 'nxs-li-'){
      $this->showAuthTop(); $at = sanitize_text_field($_GET['code']);  $ii = sanitize_text_field(str_replace('nxs-li-','',$_GET['state']));
      echo "----=={ oAuth 2.0 Wordflow }==----<br/><br/>"; 
      $gGet = $_GET; unset($gGet['code']); unset($gGet['state']); unset($gGet['post_type']); unset($gGet['activated']); unset($gGet['stylesheet']);  $sturl = explode('?',$nxs_snapSetPgURL); $nxs_snapSetPgURL = $sturl[0].((!empty($gGet))?'?'.http_build_query($gGet):'');       
      $nto = $this->nt[$ii]; $wprg = array();  $wprg['sslverify'] = false;
      if (isset($nto['appKey'])){ echo "-="; prr($nto);// die();
        $tknURL = 'https://www.linkedin.com/uas/oauth2/accessToken?grant_type=authorization_code&code='.$at.'&redirect_uri='.urlencode($nxs_snapSetPgURL).'&client_id='.nxs_gak($nto['appKey']).'&client_secret='.nxs_gas($nto['appSec']);
        $response  = nxs_remote_post($tknURL, $wprg); prr($tknURL);      
        if((is_object($response)&&(isset($response->errors)))){ prr($response); die('</div></div>'); }
        if (is_array($response)&& stripos($response['body'],'"error":')!==false){ prr($response['body']); prr(json_decode($response['body'],true)); die('</div></div>'); }
        $resp = json_decode($response['body'], true); prr($resp); if (!is_array($resp) || empty($resp['access_token'])) { prr($resp); die('</div></div>'); }
        if (function_exists('get_option')) $currTime = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ); else  $currTime = time();
        $nto['accessToken'] = $resp['access_token']; // $nto['accessTokenSec'] = 'No Need for oAuth V2'; $nto['oAuthVerifier'] = 'No Need for oAuth V2';
        $nto['accessTokenExp'] = $currTime + $resp['expires_in'];    echo "<br/>----=={ Expires: ".date('Y-m-d H:i:s', $nto['accessTokenExp'])." }==---- <br/>";
        $tknURL = 'https://api.linkedin.com/v2/me'; $hddrs=nxs_getNXSHeaders(); $hddrs['Authorization'] = 'Bearer '.$nto['accessToken']; $hddrs['X-RestLi-Protocol-Version']='2.0.0'; $response = nxs_remote_get( $tknURL, nxs_mkRemOptsArr($hddrs) ); 
         prr($tknURL); prr($response); if (is_nxs_error($response)) die('</div></div>'); $user = json_decode($response['body'], true);       prr($user); 
        if (!empty($user['id'])) { $nto['liUserID'] = $user['id'];  $nto['liUserInfo'] = $user['firstName']['localized']['en_US'].$user['lastName']['localized']['en_US'].(!empty($user['id'])?" (".$user['id'].")":'');    
          if (empty($nto['pgID'])) $nto['pgID'] = 'p'; nxs_save_glbNtwrks($ntInfo['lcode'],$ii,$nto,'*'); prr($nto['liUserInfo'], 'Authorized user');                    
          
          $gURL = 'https://api.linkedin.com/v2/organizationalEntityAcls?q=roleAssignee';  $hddrs=nxs_getNXSHeaders(); $hddrs['Authorization'] = 'Bearer '.$nto['accessToken']; $hddrs['X-RestLi-Protocol-Version']='2.0.0';          
          $response = nxs_remote_get( $gURL, nxs_mkRemOptsArr($hddrs) );  $userPages = json_decode($response['body'], true); prr($userPages, 'USER PAGES V2:'); $pgs = '';             
          if (!empty($userPages)&&!empty($userPages['elements'])) foreach ($userPages['elements'] as $e) { $ee = $e['organizationalTarget']; $ee = explode(':',$ee); $ee = end($ee);
              $gURL = 'https://api.linkedin.com/v2/organizations/'.$ee; $response = nxs_remote_get( $gURL, nxs_mkRemOptsArr($hddrs) );  $userCmp = json_decode($response['body'], true); 
              $pgs .= '<option '.($ee==$nto['pgID'] ? 'selected="selected"':'').' value="'.$ee.'">'.$userCmp['localizedName'].' ('.$ee.')</option>';
          }
          
          if (empty($pgs)) { echo "<br/>Lets Try V1<br/>"; $gURL = 'https://api.linkedin.com/v1/companies?format=json&is-company-admin=true&oauth2_access_token='.$nto['accessToken']; $response = nxs_remote_get( $gURL, nxs_mkRemOptsArr(nxs_getNXSHeaders()) );  
            prr($response);  $userPages = json_decode($response['body'], true); prr($userPages, 'USER PAGES V1:'); 
            if (!empty($userPages['values'])) foreach ($userPages['values'] as $up) $pgs .= '<option '.($up['id']==$nto['pgID'] ? 'selected="selected"':'').' value="'.$up['id'].'">'.$up['name'].' ('.$up['id'].')</option>';
          }  //die('</div></div>');
          
          
          $opVal = array(); $opNm = 'nxs_snap_li_'.sha1('nxs_snap_li'.$nto['liUserID'].nxs_gak($nto['appKey'])); $opVal['pgList'] = $pgs; nxs_saveOption($opNm, $opVal); 
          echo '<div style="text-align:center;color:green; font-weight: bold; font-size:20px;"> ALL OK. You have been authorized.</div><script type="text/javascript">setTimeout(function(){ window.location = "'.$nxs_snapSetPgURL.'"; }, 1000);</script>';
        }        
      } die('</div></div>');
    }    
  }    
  
  function getListOfPagesLIV2($networks){ $opVal = array(); if (empty($_POST['u'])) return $opVal; $opNm = 'nxs_snap_li_'.sha1('nxs_snap_li'.$_POST['u'].$_POST['p']); $opVal = nxs_getOption($opNm); $ii = sanitize_key($_POST['ii']); if (empty($networks['li'][$ii]['accessToken'])) return $opVal;
     $currPstAs = !empty($_POST['pgID'])?sanitize_text_field($_POST['pgID']):(!empty($networks['li'][$ii])?$networks['li'][$ii]['pgID']:'');
     if (empty($_POST['force']) && !empty($opVal['pgList']) ) $pgs = $opVal['pgList']; else { $options = $networks['li'][$ii]; 
         
         $gURL = 'https://api.linkedin.com/v2/organizationalEntityAcls?q=roleAssignee';  $hddrs=nxs_getNXSHeaders(); $hddrs['Authorization'] = 'Bearer '.$options['accessToken']; $hddrs['X-RestLi-Protocol-Version']='2.0.0';          
         $response = nxs_remote_get( $gURL, nxs_mkRemOptsArr($hddrs) );  $userPages = json_decode($response['body'], true); $pgs = '';             
         if (!empty($userPages)&&!empty($userPages['elements'])) foreach ($userPages['elements'] as $e) { $ee = $e['organizationalTarget']; $ee = explode(':',$ee); $ee = end($ee);
              $gURL = 'https://api.linkedin.com/v2/organizations/'.$ee; $response = nxs_remote_get( $gURL, nxs_mkRemOptsArr($hddrs) );  $userCmp = json_decode($response['body'], true); 
              $pgs .= '<option '.($ee==$options['pgID'] ? 'selected="selected"':'').' value="'.$ee.'">'.$userCmp['localizedName'].' ('.$ee.')</option>';
         }
          
         if (empty($pgs)) { echo "<br/>Lets Try V1<br/>"; $gURL = 'https://api.linkedin.com/v1/companies?format=json&is-company-admin=true&oauth2_access_token='.$options['accessToken']; $response = nxs_remote_get( $gURL, nxs_mkRemOptsArr(nxs_getNXSHeaders()) );  
            $userPages = json_decode($response['body'], true); 
            if (!empty($userPages['values'])) foreach ($userPages['values'] as $up) $pgs .= '<option '.($up['id']==$options['pgID'] ? 'selected="selected"':'').' value="'.$up['id'].'">'.$up['name'].' ('.$up['id'].')</option>';
         }
         
         
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.'<option '.($options['pgID']=='p'?'selected="selected" ':'').'value="p">'.__('Profile').'</option>'.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Page ID').'</option>';
     $opVal['pgList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  }
  
  function getListOfPagesNXS($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_li_'.sha1('nxs_snap_li'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $ii = sanitize_key($_POST['ii']); $nt = new nxsAPI_LI(); // prr($opVal);
     $currPstAs = !empty($_POST['pgcID'])?$_POST['pgcID']:(!empty($networks['li'][$ii])?$networks['li'][$ii]['pgcID']:''); $options = $networks['li'][$ii];
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal['pgsList']) ) $pgs = $opVal['pgsList']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; 
     if (!empty($message['session']) || !empty($options['session'])) { $sid = !empty($message['session'])?$message['session']:$options['session']; if (empty($nt->ck)) $nt->ck = array(); foreach ($nt->ck as $ci=>$cc) if ( $nt->ck[$ci]->name=='li_at') unset($nt->ck[$ci]);
          $c = new NXS_Http_Cookie( array('name' => 'li_at', 'value' => $sid) ); $nt->ck[] = $c; 
     } $loginError=$nt->connect(sanitize_user($_POST['u']),$_POST['p']);
       if (!$loginError){ $opVal['ck'] = $nt->ck;  $pgs = $nt->getPgsList($currPstAs); }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.strip_tags($loginError).'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Company Page ID').'</option>';
     $opVal['pgsList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  }
  function getListOfGroupsNXS($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_li_'.sha1('nxs_snap_li'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $ii = sanitize_key($_POST['ii']); $nt = new nxsAPI_LI(); // prr($opVal);
     $currPstAs = !empty($_POST['pggID'])?$_POST['pggID']:(!empty($networks['li'][$ii]['pggID'])?$networks['li'][$ii]['pggID']:''); $options = (!empty($networks['li'][$ii]))?$networks['li'][$ii]:array();
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal['grpList']) ) $pgs = $opVal['grpList']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; 
     if (!empty($message['session']) || !empty($options['session'])) { $sid = !empty($message['session'])?$message['session']:$options['session']; if (empty($nt->ck)) $nt->ck = array(); foreach ($nt->ck as $ci=>$cc) if ( $nt->ck[$ci]->name=='li_at') unset($nt->ck[$ci]);
          $c = new NXS_Http_Cookie( array('name' => 'li_at', 'value' => $sid) ); $nt->ck[] = $c; 
        }
     $loginError=$nt->connect(sanitize_user($_POST['u']),$_POST['p']);
       if (!$loginError){ $opVal['ck'] = $nt->ck;  $pgs = $nt->getGrpList($currPstAs); }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.strip_tags($loginError).'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Group ID').'</option>';
     $opVal['grpList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  }
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode'];  $ntU = $ntInfo['code']; /* prr($options); */?>
    
    <div style="display: <?php echo (empty($options['apiToUse']))?"block":"none"; ?>;">    
      <div style="width:100%; text-align: center; color:#005800; font-weight: bold; font-size: 14px;">You can choose what API you would like to use. </div>                
      <span style="color:#005800; font-weight: bold; font-size: 14px;">LinkedIn Native API V2:</span> Free built-in API from LinkedIn. oAuth 2.0. <br/><b>Can post to profiles and company pages (with approved access to Marketing API).</b> <br/><span style="color:#000080"><?php _e('Advantages', 'nxs_snap'); ?></span>: Free, Official. <br/><span style="color:#800000"><?php _e('Disadvantages', 'nxs_snap'); ?></span>: <b>Tokens expire every 30 days</b>. Can't make "image" posts. It can't post Articles and to Groups.<br/><br/>    
      <span style="color:#005800; font-weight: bold; font-size: 14px;">NextScripts API for LinkedIn:</span> Premium API with extended functionality. <br/><b>Can post Articles and to Profile, Company pages, and Groups.</b> <br/><span style="color:#000080"><?php _e('Advantages', 'nxs_snap'); ?></span>: Easier to configure. Only API that can post Articles and to Groups and make Image posts.<br/><span style="color:#800000"><?php _e('Disadvantages', 'nxs_snap'); ?></span>: Not free. Less secure - requires your password.<br/><br/>
    
      <select name="<?php echo esc_attr($nt); ?>[<?php echo esc_attr($ii); ?>][apiToUse]" onchange="jQuery('.nxs_<?php echo esc_attr($nt); ?>_apidiv_<?php echo esc_attr($ii); ?>').hide(); jQuery('.nxs_<?php echo esc_attr($nt); ?>_api'+jQuery(this).val()+'div_<?php echo esc_attr($ii); ?>').show();  ">        
        <option <?php echo (empty($options['apiToUse']) || $options['apiToUse'] =='liv2')?"selected":""; ?> value="liv2">LinkedIn Native API</option>
        <option <?php echo (!empty($options['apiToUse']) && $options['apiToUse'] =='nx')?"selected":""; ?> value="nx">NextScripts API</option>
      </select><hr/>
    
    </div>
    
    
    <div id="nxs_<?php echo esc_attr($nt); ?>_apiliv2div_<?php echo esc_attr($ii); ?>" class="nxs_<?php echo esc_attr($nt); ?>_apidiv_<?php echo esc_attr($ii); ?> nxs_<?php echo esc_attr($nt); ?>_apiliv2div_<?php echo esc_attr($ii); ?>" style="display: <?php echo (empty($options['apiToUse']) || $options['apiToUse'] =='liv2' || $options['apiToUse'] =='liv1')?"block":"none"; ?>;"><h3>LinkedIn API</h3>    
      <div class="subDiv" id="sub<?php echo esc_attr($ii); ?>DivL" style="display: block;"> <?php $this->elemKeySecret($ii,'Client ID','Client Secret', $options['appKey'], $options['appSec'],'appKey2','appSec2','https://www.linkedin.com/developer/apps'); ?><br/><br/>
      <?php if(!empty($options['accessToken'])) { 
        _e('Your '.$ntInfo['name'].' Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>User: <?php _e(apply_filters('format_to_edit', htmlentities($options['liUserInfo'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g'); ?>.
        <?php  } ?>            <br/>
        <a href="#" onclick="var url = 'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id='+jQuery('#liappKey2<?php echo esc_attr($ii); ?>').val()+'&scope=r_liteprofile+r_emailaddress+w_member_social+w_organization_social+rw_organization_admin&state=nxs-li-<?php echo esc_attr($ii); ?>&redirect_uri=<?php echo trim(urlencode($nxs_snapSetPgURL));?>'; nxs_svSetAdv('<?php echo esc_attr($nt); ?>', '<?php echo esc_attr($ii); ?>', '<?php echo $isNew?'dom'.$ntU.$ii.'Div':'nxsAllAccntsDiv'; ?>','nxs<?php echo $ntU; ?>MsgDiv<?php echo esc_attr($ii); ?>',url,'1'); return false;">Authorize Your LinkedIn Account (<b>with</b> Marketing API)</a>        
        <br/>
        <a href="#" onclick="var url = 'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id='+jQuery('#liappKey2<?php echo esc_attr($ii); ?>').val()+'&scope=r_liteprofile+r_emailaddress+w_member_social&state=nxs-li-<?php echo esc_attr($ii); ?>&redirect_uri=<?php echo trim(urlencode($nxs_snapSetPgURL));?>'; nxs_svSetAdv('<?php echo esc_attr($nt); ?>', '<?php echo esc_attr($ii); ?>', '<?php echo $isNew?'dom'.$ntU.$ii.'Div':'nxsAllAccntsDiv'; ?>','nxs<?php echo $ntU; ?>MsgDiv<?php echo esc_attr($ii); ?>',url,'1'); return false;">Authorize Your LinkedIn Account (<b>without</b> Marketing API)</a>        
        <?php if (empty($options['accessToken'])) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php } ?><br/><br/>
      </div>
      
      <?php if (empty($options['liUserID'])) $options['liUserID'] = ''; //## List of Pages
    $opNm = 'nxs_snap_li_'.sha1('nxs_snap_li'.$options['liUserID'].nxs_gak($options['appKey'])); $opVal = nxs_getOption($opNm); 
    if (empty($opVal)) { $tPST = (!empty($_POST))?$_POST:'';  $_POST['pgID'] = $options['pgID']; $_POST['u'] = $options['liUserID']; $_POST['p'] = nxs_gak($options['appKey']); $_POST['ii'] = $ii; $ntw[$nt][$ii]=$options; $opVal = $this->getListOfPagesLIV2($ntw); $_POST = $tPST; }
    if (!empty($opVal) & !is_array($opVal)) $options['uMsg'] = $opVal; else { if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
  ?><br/ ><div style="width:100%;"><b><?php _e('Where to Post', 'nxs_snap'); ?></b>&nbsp;(<?php _e('Please select your Profile or Company Page', 'nxs_snap'); ?>)</div>
    <div id="nxsLIInfoDiv<?php echo esc_attr($ii); ?>" style="<?php echo empty($options['accessToken'])?'display:none;':''; ?>">
         <div style="width:100%;">
          <div>                   
          <select id="lipgID<?php echo esc_attr($ii); ?>" onchange="nxs_liPageChange('<?php echo esc_attr($ii);?>',jQuery(this));" name="li[<?php echo esc_attr($ii);?>][pgID]">
            <?php $pgi = !empty($options['pgList'])?$options['pgList']:''; 
              if (!empty($options['pgID'])) { echo (!empty($options['pgID']) && stripos($pgi,$options['pgID'])===false)?'<option selected="selected" value="'.$options['pgID'].'">'.$options['pgID'].'</option>':''; }            
              if (!empty($options['pgID'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['pgID'].'"','selected="selected" value="'.$options['pgID'].'"',$pgi); }               
              echo '<option '.($options['pgID']=='p'?'selected="selected" ':'').'value="p">'.__('Profile').'</option>'; echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Page ID'); ?></option>
          </select><div id="nxsLIInfoDivBlock<?php echo esc_attr($ii); ?>" style="display: inline-block;"> <input type="text" style="display: none;" id="liInpCst<?php echo esc_attr($ii); ?>" value="<?php echo $options['pgID']; ?>" onchange="nxs_InpToDDChange(jQuery(this));" data-tid="lipgID<?php echo esc_attr($ii); ?>" />         
          <div style="display: inline-block;"><a onclick="nxs_liGetPages(<?php echo esc_attr($ii);?>, 1); jQuery(this).blur(); return false;" href="#"><img id="<?php echo esc_attr($nt.$ii);?>rfrshImg" style="vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/refresh16.png' /></a></div></div> <img id="<?php echo esc_attr($nt.$ii);?>ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div> <div id="nxsLIMsgDiv<?php echo esc_attr($ii); ?>"><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?><?php if ($isNew) { ?><?php _e('Please authorize your account', 'nxs_snap'); ?><?php } ?></div>                                                                                                    
    </div> <input type="hidden" id="liAuthUser<?php echo esc_attr($ii); ?>" value="<?php echo !empty($options['authUser'])?$options['authUser']:''; ?>"/> <br/>
      
      
    </div>
    <div id="nxs_<?php echo esc_attr($nt); ?>_apinxdiv_<?php echo esc_attr($ii); ?>" class="nxs_<?php echo esc_attr($nt); ?>_apidiv_<?php echo esc_attr($ii); ?> nxs_<?php echo esc_attr($nt); ?>_apinxdiv_<?php echo esc_attr($ii); ?>" style="display: <?php echo (!empty($options['apiToUse']) && $options['apiToUse'] =='nx')?"block":"none"; ?>;"><h3>NextScripts API</h3>
    
    <?php if (class_exists('nxsAPI_LI')) { if (!empty($options['uPass'])&&!empty($options['uName'])) { $opNm = 'nxs_snap_li_'.sha1('nxs_snap_li'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm);
      if (empty($opVal)){$tPST=(!empty($_POST))?$_POST:''; if (empty($_POST)) $_POST = array(); $_POST['pgcID']=!empty($options['pgcID'])?$options['pgcID']:'';  $_POST['pggID']=!empty($options['pggID'])?$options['pggID']:''; 
      if(!empty($options['uPass'])){ $_POST['u']=$options['uName']; $_POST['p']=$options['uPass']; $_POST['ii']=$ii; $ntw[$nt][$ii]=$options; $opVal = $this->getListOfPagesNXS($ntw); $opVal = $this->getListOfGroupsNXS($ntw); }$_POST = $tPST; }
      if (!empty($opVal) & !is_array($opVal)) $options['uMsg'] = $opVal; else { if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); }
    } if (empty($options['uPass'])) $options['uPass'] = ''; if (empty($options['uName'])) $options['uName'] = ''; if (empty($options['session'])) $options['session'] = ''; 
    ?>
    <div class="subDiv" id="sub<?php echo esc_attr($ii); ?>DivN" style="display: block;"><?php $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?></div><br/>
    
    <div style="width:100%;"><strong><?php _e('Session ID (li_at)', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> 
       <div style="font-size: 11px; margin: 0px;"><?php _e('[Optional] Please use this only if you are having troubles to login/post without it.', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
    </div>    
    <input style="width:400px;" name="<?php echo esc_attr($nt); ?>[<?php echo esc_attr($ii); ?>][session]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['session'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /> 
    <br/><br/>   
    
    <script type="text/javascript">      
      jQuery('#apLIUName<?php echo esc_attr($ii); ?>').change(function() { var u = jQuery(this).val();  var p = jQuery('#apLIPass<?php echo esc_attr($ii); ?>').val(); if( u!='' && p!='' ) { nxs_li2GetPages(<?php echo esc_attr($ii); ?>,0); }  });
      jQuery('#apLIPass<?php echo esc_attr($ii); ?>').change(function() { var u = jQuery('#apLIUName<?php echo esc_attr($ii); ?>').val();  var p = jQuery(this).val(); if( u!='' && p!='' ) { nxs_li2GetPages(<?php echo esc_attr($ii); ?>,0); }  });
      
      jQuery('.liWhereToPost<?php echo esc_attr($ii); ?>').change(function() { if (jQuery(this).val()!='P') jQuery('#liPostType<?php echo esc_attr($ii); ?>').show(); else jQuery('#liPostType<?php echo esc_attr($ii); ?>').hide();  });
      
    </script>
    <div style="width:100%;"><b style="font-size: 15px;"><?php _e('Where to Post', 'social-networks-auto-poster-facebook-twitter-g'); if (empty($options['whToPost'])) $options['whToPost'] = 'PR'; ?>:</b> </div>
      <div style="margin-left: 10px;">        
        <input class="liWhereToPost<?php echo esc_attr($ii); ?>" type="radio" name="li[<?php echo esc_attr($ii); ?>][whToPost]" value="PR" <?php if ($options['whToPost'] == 'PR') echo 'checked="checked"'; ?> /> <?php _e('Profile Update', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post to your profile', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>
        <input class="liWhereToPost<?php echo esc_attr($ii); ?>" type="radio" name="li[<?php echo esc_attr($ii); ?>][whToPost]" value="C" <?php if ($options['whToPost'] == 'C') echo 'checked="checked"'; ?> /> <?php _e('Company Page', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post to Company page', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/> 
        
        <div style="margin-left: 30px;<?php echo (empty($options['uName']) && empty($options['uPass']))?'display:none;':''; ?>" id="nxsLI2InfoDiv<?php echo esc_attr($ii); ?>"> 
         <div style="width:100%;">
          <div>                   
          <select id="li2pgID<?php echo esc_attr($ii); ?>" onchange="nxs_li2PageChange('<?php echo esc_attr($ii);?>',jQuery(this));" name="li[<?php echo esc_attr($ii);?>][pgcID]">
            <?php $pgi = !empty($options['pgsList'])?$options['pgsList']:''; 
              if (!empty($options['pgcID'])) { echo (!empty($options['pgcID']) && stripos($pgi,$options['pgcID'])===false)?'<option selected="selected" value="'.$options['pgcID'].'">'.$options['pgcID'].'</option>':''; }            
              if (!empty($options['pgcID'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['pgcID'].'"','selected="selected" value="'.$options['pgcID'].'"',$pgi); 
                $pgi = str_ireplace('data-val="'.$options['pgcID'].'"','selected="selected" data-val="'.$options['pgcID'].'"',$pgi);
              }               
              echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Company Page ID'); ?></option>
          </select>
          <div id="nxsLI2InfoDivBlock<?php echo esc_attr($ii); ?>" style="display: inline-block;">
          <input type="text" style="display: none;" id="li2InpCst<?php echo esc_attr($ii); ?>" value="<?php echo $options['pgcID']; ?>" onblur="nxs_InpToDDBlur(jQuery(this));"  onchange="nxs_InpToDDChange(jQuery(this));" data-tid="li2pgID<?php echo esc_attr($ii); ?>" />         
          <div style="display: inline-block;"><a onclick="nxs_li2GetPages(<?php echo esc_attr($ii);?>, 1); jQuery(this).blur(); return false;" href="#"><img id="<?php echo esc_attr($nt.$ii);?>2rfrshImg" style="vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/refresh16.png' /></a></div></div> <img id="<?php echo esc_attr($nt.$ii);?>2ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div> <div id="nxsLI2MsgDiv<?php echo esc_attr($ii); ?>"><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?><?php if ($isNew) { ?><i style="color: #800080"><?php _e('Please Enter your username and password to select your page', 'nxs_snap'); ?><?php } ?></i></div>                                                                          
        </div> 
        <input class="liWhereToPost<?php echo esc_attr($ii); ?>" type="radio" name="li[<?php echo esc_attr($ii); ?>][whToPost]" value="G" <?php if ($options['whToPost'] == 'G') echo 'checked="checked"'; ?> /> <?php _e('Group', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post to LinkedIn Group', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/> 
        
        <div style="margin-left: 30px;<?php echo (empty($options['uName']) && empty($options['uPass']))?'display:none;':''; ?>" id="nxsLI2GInfoDiv<?php echo esc_attr($ii); ?>"> 
         <div style="width:100%;">
          <div>                   
          <select id="li2GpgID<?php echo esc_attr($ii); ?>" onchange="nxs_li2GPageChange('<?php echo esc_attr($ii);?>',jQuery(this));" name="li[<?php echo esc_attr($ii);?>][pggID]">
            <?php $pgi = !empty($options['grpList'])?$options['grpList']:''; 
              if (!empty($options['pggID'])) { echo (!empty($options['pggID']) && stripos($pgi,$options['pggID'])===false)?'<option selected="selected" value="'.$options['pggID'].'">'.$options['pggID'].'</option>':''; }            
              if (!empty($options['pggID'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['pggID'].'"','selected="selected" value="'.$options['pggID'].'"',$pgi); 
                $pgi = str_ireplace('data-val="'.$options['pggID'].'"','selected="selected" data-val="'.$options['pggID'].'"',$pgi);
              }               
              echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Group ID'); ?></option>
          </select>
          <div id="nxsLI2GInfoDivBlock<?php echo esc_attr($ii); ?>" style="display: inline-block;">
          <input type="text" style="display: none;" id="li2GInpCst<?php echo esc_attr($ii); ?>" value="<?php echo $options['pgcID']; ?>" onblur="nxs_InpToDDBlur(jQuery(this));"  onchange="nxs_InpToDDChange(jQuery(this));" data-tid="li2GpgID<?php echo esc_attr($ii); ?>" />         
          </div> <img id="<?php echo esc_attr($nt.$ii);?>3ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div>  
          <?php // $this->elemTitleFormat($ii,'Group Title Format','msgTFormat',empty($options['msgTFormat'])?'%TITLE%':$options['msgTFormat']); ?>                                                                         
        </div>                 
        <input class="liWhereToPost<?php echo esc_attr($ii); ?>" type="radio" name="li[<?php echo esc_attr($ii); ?>][whToPost]" value="P" <?php if ($options['whToPost'] == 'P') echo 'checked="checked"'; ?> /> <?php _e('Article', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Rich text post article shared to the "Articles" section', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>
        
         <div style="margin-left: 30px;">            
        <?php $this->elemTitleFormat($ii,'Article Title Format','msgCTFormat',empty($options['msgCTFormat'])?'%TITLE%':$options['msgCTFormat']); ?> <?php $this->elemMsgFormat($ii,'Article Text Format','msgCFormat',empty($options['msgCFormat'])?'%RAWTEXT%':$options['msgCFormat']); ?>      
        </div>
        
        </div>
        
        <br/>     
          
    <?php } else { nxs_show_noLibWrn('"NextScripts API Library for LinkedIN" is NOT installed'); } ?>           
    </div><br/>
    
    <?php $this->elemMsgFormat($ii,'Message Format','msgFormat',$options['msgFormat']); ?>
    
    <div id="liPostType<?php echo esc_attr($ii); ?>" style="<?php echo (!empty($options['whToPost']) && $options['whToPost']=='P')?'display:none;':''; ?>">
    <div style="width:100%;"><strong id="altFormatText">Post Type:</strong> </div>                      
            <div style="margin-left: 10px;">
        <?php if(empty($options['postType'])) {if (( !empty($options['liAttch']) && (int)$options['liAttch'] == 1) || $isNew) $options['postType'] = 'A';} ?>
        <input class="liPostType<?php echo esc_attr($ii); ?>" type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="T" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                    
        <span class="nxs_li_nxapi_<?php echo esc_attr($ii); ?> nxs_<?php echo esc_attr($nt); ?>_apidiv_<?php echo esc_attr($ii); ?> nxs_<?php echo esc_attr($nt); ?>_apinxdiv_<?php echo esc_attr($ii); ?>" style="display: <?php echo (!empty($options['apiToUse']) && $options['apiToUse'] =='nx')?"block":"none"; ?>;">
        <input class="liPostType<?php echo esc_attr($ii); ?>" type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="I" <?php if ($options['postType'] == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('big image with text message (Profiles and Company pages only)', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>  </span> 
        <input class="liPostType<?php echo esc_attr($ii); ?>" type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="A" <?php if ( empty($options['postType']) || $options['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Add blogpost to LinkedIn message as an attachment', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
        
       
        
      <div style="margin-left: 10px;">            
        <strong><?php _e('Attachment Text Format', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><br/> 
      <input value="1"  id="apLIMsgAFrmtA<?php echo esc_attr($ii); ?>" <?php if (empty($options['msgAFormat']) || trim($options['msgAFormat'])=='' ) echo "checked"; ?> onchange="if (jQuery(this).is(':checked')) { jQuery('#apLIMsgAFrmtDiv<?php echo esc_attr($ii); ?>').hide(); jQuery('#apLIMsgAFrmt<?php echo esc_attr($ii); ?>').val(''); }else jQuery('#apLIMsgAFrmtDiv<?php echo esc_attr($ii); ?>').show();" type="checkbox" name="li[<?php echo esc_attr($ii); ?>][msgAFormatCB]"/> <strong><?php _e('Auto', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong>
      <i> - <?php _e('Recommended. Info from SEO Plugins will be used, then post excerpt, then post text', 'social-networks-auto-poster-facebook-twitter-g'); ?> </i><br/>
      <div id="apLIMsgAFrmtDiv<?php echo esc_attr($ii); ?>" style="<?php  if (empty($options['msgAFormat']) || trim($options['msgAFormat'])=='' ) echo "display:none;"; ?>" >
      <?php $this->elemTitleFormat($ii,'Title Format','msgATFormat',$options['msgATFormat']); $this->elemMsgFormat($ii,'Message Format','msgAFormat',$options['msgAFormat']); ?>
      
      </div>            
      </div>
      
      </div>
        
   </div><br/>   
    
     <?php
  }
  function advTab($ii, $options){ $this->askForSURL( $this->ntInfo['lcode'], $ii, $options);  $this->showProxies($this->ntInfo['lcode'], $ii, $options); }                             
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ $otp = array(); //prr($options);
    foreach ($options as $oo => $v){  if (isset($v['ck'])) unset($v['ck']);
        if (isset($oo) && $oo!=='' && ((!empty($v['appKey']) && !empty($v['appSec'])) || (!empty($v['appKey2']) && !empty($v['appSec2'])) || (!empty($v['uPass']) && !empty($v['uName']))) ) $otp[$oo] = $v;
    } $options = $otp;  //  prr($options);
    foreach ($post as $ii => $pval){ //  prr($ii, 'II');    prr($pval);
      if ( (!empty($pval['appKey']) && !empty($pval['appSec'])) || (!empty($pval['appKey2']) && !empty($pval['appSec2'])) || (!empty($pval['uPass']) && !empty($pval['uName'])) ){ 
        if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['apiToUse'])) $options[$ii]['apiToUse'] = trim($pval['apiToUse']);
        if (isset($pval['msgCFormat'])) $options[$ii]['msgCFormat'] = trim($pval['msgCFormat']);
        if (isset($pval['msgCTFormat'])) $options[$ii]['msgCTFormat'] = trim($pval['msgCTFormat']);
        if ($options[$ii]['apiToUse']=='liv2') {
          if (isset($pval['appKey2'])) $options[$ii]['appKey'] = trim($pval['appKey2']);
          if (isset($pval['appSec2'])) $options[$ii]['appSec'] = trim($pval['appSec2']);      
        }
        if (isset($pval['whToPost'])) $options[$ii]['whToPost'] = trim($pval['whToPost']);  
        if (isset($pval['pgcID'])) $options[$ii]['pgcID'] = trim($pval['pgcID']);  
        if (isset($pval['pggID'])) $options[$ii]['pggID'] = trim($pval['pggID']);  
        if (isset($pval['pgID'])) $options[$ii]['pgID'] = trim($pval['pgID']);  
        if (isset($pval['grpID'])) $options[$ii]['grpID'] = trim($pval['grpID']);
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
        
        $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); 
        
          if ($ntOpt['whToPost']=='G') $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);          
          $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);  
          
          if ($ntOpt['whToPost']!='P') {  if (!empty($ntOpt['apiToUse']) && $ntOpt['apiToUse'] !='nx' && !empty($ntOpt['postType']) && $ntOpt['postType'] == 'I') $ntOpt['postType'] = 'A';
          ?>
           <tr style="<?php echo !empty($ntOpt['do'])?'display:table-row;':'display:none;'; ?>" class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> 
                </th><td>     
        
        <input type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="T" <?php if (!empty($ntOpt['postType']) && $ntOpt['postType'] == 'T') echo 'checked="checked"'; ?> /><?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>       
        <span class="nxs_li_nxapi_<?php echo esc_attr($ii); ?>" style="display: <?php echo (!empty($ntOpt['apiToUse']) && $ntOpt['apiToUse'] =='nx')?"block":"none"; ?>;">
        <input type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="I" <?php if (!empty($ntOpt['postType']) && $ntOpt['postType'] == 'I') echo 'checked="checked"'; ?> onchange="jQuery('#altFormatIMG<?php echo esc_attr($nt.$ii);?>').show();" /> <?php _e('Post to LinkedIn as "Image post"', 'social-networks-auto-poster-facebook-twitter-g') ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/> </span>            
        <input type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="A" <?php if ( empty($ntOpt['postType']) || $ntOpt['postType'] == 'A') echo 'checked="checked"'; ?> onchange="jQuery('#altFormatIMG<?php echo esc_attr($nt.$ii);?>').hide();" /><?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?>        
     </td></tr>          <?php } else {
          $this->elemEdTitleFormat($ii, __('Article Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),htmlentities($ntOpt['msgCTFormat']));          
          $this->elemEdMsgFormat($ii, __('Article Text Format:', 'social-networks-auto-poster-facebook-twitter-g'),htmlentities($ntOpt['msgCFormat']));  
         
     }
          nxs_showImgToUseDlg($nt, $ii, $imgToUse);        
    
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  function showEdPostNTSettingsV4($ntOpt, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $ii = $ntOpt['ii']; //prr($ntOpt['postType']);
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; 
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse'];
        
        //if ($ntOpt['whToPost']=='G') $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);          
        
        $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);  
          
        if ($ntOpt['whToPost']!='P') {  if (!empty($ntOpt['apiToUse']) && $ntOpt['apiToUse'] !='nx' && !empty($ntOpt['postType']) && $ntOpt['postType'] == 'I') $ntOpt['postType'] = 'A';
        ?>
        
  <div class="nxsPostEd_ElemWrap">   
     <div class="nxsPostEd_ElemLabel"><?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>   
     <div class="nxsPostEd_Elem">   
         <input type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="T" class="nxsEdElem nxsImgCtrlCb" data-nt="<?php echo esc_attr($nt); ?>" data-ii="<?php echo esc_attr($ii); ?>" <?php if (!empty($ntOpt['postType']) && $ntOpt['postType'] == 'T') echo 'checked="checked"'; ?> /><?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>       
        <span class="nxs_li_nxapi_<?php echo esc_attr($ii); ?>" style="display: <?php echo (!empty($ntOpt['apiToUse']) && $ntOpt['apiToUse'] =='nx')?"block":"none"; ?>;">
        <input type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="I" class="nxsEdElem nxsImgCtrlCb" data-nt="<?php echo esc_attr($nt); ?>" data-ii="<?php echo esc_attr($ii); ?>" <?php if (!empty($ntOpt['postType']) && $ntOpt['postType'] == 'I') echo 'checked="checked"'; ?> onchange="jQuery('#altFormatIMG<?php echo esc_attr($nt.$ii);?>').show();" /> <?php _e('Post to LinkedIn as "Image post"', 'social-networks-auto-poster-facebook-twitter-g') ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/> </span>            
        <input type="radio" name="li[<?php echo esc_attr($ii); ?>][postType]" value="A" class="nxsEdElem nxsImgCtrlCb" data-nt="<?php echo esc_attr($nt); ?>" data-ii="<?php echo esc_attr($ii); ?>" <?php if ( empty($ntOpt['postType']) || $ntOpt['postType'] == 'A') echo 'checked="checked"'; ?> onchange="jQuery('#altFormatIMG<?php echo esc_attr($nt.$ii);?>').hide();" /><?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?> 
     </div>     
   </div>  <?php } else {
          $this->elemEdTitleFormat($ii, __('Article Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),htmlentities($ntOpt['msgCTFormat']));          
          $this->elemEdMsgFormat($ii, __('Article Text Format:', 'social-networks-auto-poster-facebook-twitter-g'),htmlentities($ntOpt['msgCFormat']));  
         
     }
     // ## Select Image & URL 
     nxs_showImgToUseDlg($nt, $ii, $imgToUse, !($ntOpt['postType'] == 'I'));
     nxs_showURLToUseDlg($nt, $ii, $urlToUse);       

  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
    
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ //prr($message); prr($options);
    if (!empty($postID)) { if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; } $message['imageURL'] = $imgURL; $post = get_post($postID);            
      $addParams = nxs_makeURLParams(array('NTNAME'=>$this->ntInfo['name'], 'NTCODE'=>$this->ntInfo['code'], 'POSTID'=>$postID, 'ACCNAME'=>$options['nName'])); 
      if (!empty($options['msgCFormat'])) $options['msgCFormat'] = nsFormatMessage( $options['msgCFormat'], $postID, $addParams);
      if (!empty($options['msgCTFormat'])) $options['msgCTFormat'] = nsFormatMessage( $options['msgCTFormat'], $postID, $addParams);
      if ($options['postType']=='A'){ $lng = '';
        //## AUTO - Get Post Descr from SEO Plugins or make it.      
        if (!empty($options['msgAFormat'])) { $dsc = nsFormatMessage($options['msgAFormat'], $postID, $addParams); $urlTitle = (!empty($options['msgATFormat']))?nsFormatMessage($options['msgATFormat'], $postID, $addParams): nxs_doQTrans($post->post_title, $lng);  } 
        else { if (function_exists('aioseop_mrt_fix_meta') && empty($dsc))  $dsc = trim(get_post_meta($postID, '_aioseop_description', true)); 
          if (function_exists('wpseo_admin_init') && empty($dsc)) $dsc = trim(get_post_meta($postID, '_yoast_wpseo_opengraph-description', true));  
          if (function_exists('wpseo_admin_init') && empty($dsc)) $dsc = trim(get_post_meta($postID, '_yoast_wpseo_metadesc', true));      
          if (empty($dsc)) $dsc = trim(nxs_doQTrans($post->post_excerpt, $lng)); 
          if (empty($dsc)) $dsc = trim(nxs_doQTrans($post->post_content, $lng));  
          global $nxs_SNAP; $gOptions = $nxs_SNAP->nxs_options;if (empty($gOptions['brokenCntFilters'])) $dsc = apply_filters('the_content', $dsc);
          if (empty($dsc)) $dsc = get_bloginfo('description'); $urlTitle = nxs_doQTrans($post->post_title, $lng); 
        } $dsc = strip_tags(strip_shortcodes($dsc));// $dsc = nxs_decodeEntitiesFull($dsc); /## This is commented out to support Emoji in Link Description
        $dsc = nsTrnc($dsc, 900, ' '); $message['urlDescr'] = $dsc; if (!empty($urlTitle)) $message['urlTitle'] = strip_tags(strip_shortcodes($urlTitle)); 
      }
    }
  }

}}
function adjAfterPost(&$options, &$ret){ if ($ret['isPosted']=='1') nxs_save_glbNtwrks('li', $options['ii'], '', 'session'); }   

if (!function_exists("nxs_doPublishToLI")) { function nxs_doPublishToLI($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassLI(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); }} 

?>
