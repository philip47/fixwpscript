<?php
//## NextScripts Facebook Connection Class
$nxs_snapAvNts[] = array('code'=>'WP', 'lcode'=>'wp', 'name'=>'WP Based Blog', 'type'=>'Blogs/Publishing Platforms', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Auto-submit your blogpost to another WordPress based site. Support for any standalone Wordpress and WordPress.com, Blog.com, etc..');

if (!class_exists("nxs_snapClassWP")) { class nxs_snapClassWP extends nxs_snapClassNT {
    var $ntInfo = array('code'=>'WP', 'lcode'=>'wp', 'name'=>'WP Based Blog', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'https://www.nextscripts.com/setup-installation-wp-based-social-networks-auto-poster-wordpress/');

    function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
        case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];
            $ntOptsOut['msgTFormat'] = $ntOpts['wpMsgTFormat']; $ntOptsOut['msgFormat'] = $ntOpts['wpMsgFormat'];  $ntOptsOut['uName'] = $ntOpts['wpUName'];  $ntOptsOut['uPass'] = $ntOpts['wpPass'];
            $ntOptsOut['wpURL'] = $ntOpts['wpURL'];
            break;
    }
        return !empty($ntOptsOut)?$ntOptsOut:$ntOpts;
    }

    //#### Show Common Settings
    function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }
    //#### Show NEW Settings Page
    function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'wpURL'=>'', 'inclCats'=>'1', 'inclTags'=>'1',  'msgTFormat'=>'%TITLE%', 'msgFormat'=>"%RAWTEXT%", 'pt'=>'post', 'exactCopy'=>true); $this->showGNewNTSettings($ii, $defO); }
    //#### Show Unit  Settings
    function checkIfSetupFinished($options) { return !empty($options['uPass']); }
    function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; if (!isset($options['inclTags'])) $options['inclTags'] = 1; if (!isset($options['inclCats'])) $options['inclCats'] = 1;
        if (empty($options['pt'])) $options['pt'] = 'post';
        ?><div style="width:100%;"><strong><?php _e('Where to Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong>&nbsp;<i><?php _e('XMLRPC URL', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input style="width: 550px;" name="wp[<?php echo esc_attr($ii); ?>][wpURL]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['wpURL'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>
        <div style="font-size: 11px; margin: 0px;">[Any Wordpress Based Blog] Usually its a URL of your Wordpress installation with /xmlrpc.php at the end.<br/> [Wordpress.com] Please use <b style="color: #005800;">https://YourUserName.wordpress.com/xmlrpc.php</b> (replace YourUserName with your user name) <br/>[Blog.com] Please use <b style="color: #005800;">http://YourUserName.blog.com/xmlrpc.php</b> (replace YourUserName with your user name)</div><br/>

        <?php $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?><br/>

        <input type="checkbox" name="wp[<?php echo esc_attr($ii); ?>][exactCopy]" <?php if (!empty($options['exactCopy'])) echo "checked"; ?> onchange="if (jQuery(this).is(':checked')) {jQuery('#moreOptions<?php echo esc_attr($nt.$ii); ?>').hide(); jQuery('#moreOptionsExC<?php echo esc_attr($nt.$ii); ?>').show();}else{jQuery('#moreOptions<?php echo esc_attr($nt.$ii); ?>').show();jQuery('#moreOptionsExC<?php echo esc_attr($nt.$ii); ?>').hide();}" /> Make exact copy.<br/> <br/>

        <div id="moreOptions<?php echo esc_attr($nt.$ii); ?>" style="<?php if (!empty($options['exactCopy'])) echo "display:none;"; ?>">

            <?php $this->elemTitleFormat($ii,'Post Title Format','msgTFormat',$options['msgTFormat']); $this->elemMsgFormat($ii,'Post Text Format','msgFormat',$options['msgFormat']);?><br/ >

            <div style="width:100%;"><strong><?php _e('Post Type', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong>&nbsp;</div><input style="width: 550px;" name="wp[<?php echo esc_attr($ii); ?>][pt]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['pt'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>
            <br/>

            <div style="margin-bottom: 20px;margin-top: 5px;"><input value="1" type="checkbox" name="<?php echo esc_attr($nt); ?>[<?php echo esc_attr($ii); ?>][inclTags]"  <?php if (!empty($options['inclTags'])) echo "checked"; ?> />
                <strong><?php _e('Post with tags', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong> <?php _e('Tags from the blogpost will be included', 'social-networks-auto-poster-facebook-twitter-g'); ?>
            </div>
            <div style="margin-bottom: 20px;margin-top: 5px;"><input value="1" type="checkbox" name="<?php echo esc_attr($nt); ?>[<?php echo esc_attr($ii); ?>][inclCats]"  <?php if (!empty($options['inclCats'])) echo "checked"; ?> />
                <strong><?php _e('Post with categories', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong> <?php _e('Categories from the blogpost will be included. Target blog must have the categories with the same slugs', 'social-networks-auto-poster-facebook-twitter-g'); ?>
            </div>
        </div>

        <div id="moreOptionsExC<?php echo esc_attr($nt.$ii); ?>" style="<?php if (empty($options['exactCopy'])) echo "display:none;"; ?>">
            &nbsp;&nbsp;&nbsp;<input type="checkbox" name="wp[<?php echo esc_attr($ii); ?>][keepDate]" <?php if (!empty($options['keepDate'])) echo "checked"; ?>/> Keep Original Date<br/><br/>
        </div>
        <?php
    }
    function advTab($ii, $options){}
    //#### Set Unit Settings from POST
    function setNTSettings($post, $options){
        foreach ($post as $ii => $pval){
            if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
                if (isset($pval['wpURL']))  $options[$ii]['wpURL'] = trim($pval['wpURL']); if (substr($options[$ii]['wpURL'], 0, 4)!=='http') $options[$ii]['wpURL'] = 'http://'.$options[$ii]['wpURL']; // prr($options[$ii]['wpURL']);
                if (isset($pval['pt']))  $options[$ii]['pt'] = trim($pval['pt']);
                if (isset($pval['exactCopy']))  $options[$ii]['exactCopy'] = true; else $options[$ii]['exactCopy'] = false;
                if (!empty($pval['keepDate']))  $options[$ii]['keepDate'] = true; else $options[$ii]['keepDate'] = false;
            } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0;
        } return $options;
    }

    //#### Show Post->Edit Meta Box Settings
    function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
        foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue;
            $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);

            if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
            $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
            $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse'];  $ntOpt['ii']=$ii;

            $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); ?>
            <?php  $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);  $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);
            /* ## Select Image & URL ## */ nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);
        }
    }


    function showEdPostNTSettingsV4($ntOpt, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $ii = $ntOpt['ii']; //prr($ntOpt['postType']);
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;
        ?><div id="moreOptions<?php echo esc_attr($nt.$ii); ?>" style="<?php if (!empty($ntOpt['exactCopy'])) echo "display:none;"; ?>"><?php
        //## Title and Message
        $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);
        $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);
        // ## Select Image & URL
        nxs_showImgToUseDlg($nt, $ii, $imgToUse);
        nxs_showURLToUseDlg($nt, $ii, $urlToUse);

        ?></div><div style="padding:15px;<?php if (empty($ntOpt['exactCopy'])) echo "display:none;"; ?>">&nbsp;&nbsp;&nbsp;### "Make Exact Copy" is ON. No additional options available.</div><?php
    }

    //#### Save Meta Tags to the Post
    function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);  //   prr($optMt);

        return $optMt;
    }

    function adjPublishWP(&$options, &$message, $postID){
        $message['pid'] = $postID;
    }
}}

if (!function_exists("nxs_doPublishToWP")) { function nxs_doPublishToWP($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true));
    $cl = new nxs_snapClassWP(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID);
}}
?>