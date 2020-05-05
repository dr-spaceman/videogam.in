<?php

namespace Vgsite;

class Badges extends DomainObject
{
    public function __construct() {
        //???
        $this->values = array("garbage", "bronze", "silver", "gold", "gold", "gold", "gold");
    } 

    public static function findById(int $id): ?DomainObject
    {

    }

    public static function earn(int $badge_id, User $user)
    {
        //make sure hasn't already earned
        $sql = sprintf("SELECT (*) FROM badges_earned WHERE badge_id='%d' AND user_id='%d' LIMIT 1", $badge_id, $user->getId());
        if(mysqli_num_rows(mysqli_query($GLOBALS['db']['link'], $q))) return false;
        
        $badge = static::findById($badge_id);
        
        $q = "INSERT INTO badges_earned (user_id, badge_id, `datetime`) VALUES ('$user->getId()', '$badge_id', '".date("Y-m-d H:i:s")."');";
        if(!mysqli_query($GLOBALS['db']['link'], $q)) return false;
        
        if ($user->getId() == $GLOBALS['user_id']) $_SESSION['newbadges'][] = $badge_id;
        
        if ($badge_id == 1) return true;

        // Insert stream
        $url = '/~'.$user->getUsername().'/#/badges/'.$badge_id.'/'.formatNameURL($badge->getName());
        $action = '[[User:'.$user->getUsername().']] earned the <a href="'.$url.'">'.$badge->getName().'</a> badge.'.
                '<div class="badge"><a href="'.$url.'"><img src="/bin/img/badges/'.$badge_id.'.png" alt="badge: '.htmlSC($badge->getName()).'" width="50" height="50"/></a></div>';
        Userstream::insert($action, 'earn badge', $user->getId());
        
        return true;

    }
    
    function get($badge_id){
        
        // look up badge info and est it as $this->badges[BADGE ID]
        
        if($this->badges[$badge_id]) return;
        $query = "SELECT * FROM badges WHERE badge_id='".mysqli_real_escape_string($GLOBALS['db']['link'], $badge_id)."' LIMIT 1";
        $res   = mysqli_query($GLOBALS['db']['link'], $query);
        while($row = mysqli_fetch_assoc($res)){
            $this->badges[$row['badge_id']] = $row;
            $this->badges[$row['badge_id']]['value2'] = $this->values[$row['value']];
        }
    }
    
    function showEarned($badge_id){
        
        //show an earned badge
        
        $this->get($badge_id);
        
        $ret = '
        <div class="badge badgeearn">
            <div class="container">
                <a href="#close" class="preventdefault ximg close">close</a>
                <h5>You earned a new badge!</h5>
                <h6>'.$this->badges[$badge_id]['name'].'</h6>
                <div class="badgeimg"><img src="/bin/img/badges/'.$badge_id.'.png" alt="badge: '.htmlSC($this->badges[$badge_id]['name']).'"/></div>
                <big>'.bb2html($this->badges[$badge_id]['description']).'</big>
                <div class="message"><b>Congratulations'.($GLOBALS['usrname'] ? ', '.$GLOBALS['usrname'] : '').'!</b> This <span class="value '.$this->badges[$badge_id]['value2'].'">'.ucwords($this->badges[$badge_id]['value2']).' badge</span> will be shown on your profile page along with dozens of other badges you can earn for contributing at Videogam.in.</div>
            </div>
        </div>
        ';
        
        if($GLOBALS['user_id']){
            //mark this badge as shown
            $q = "UPDATE badges_earned SET `new` = '0' WHERE badge_id = '".mysqli_real_escape_string($GLOBALS['db']['link'], $badge_id)."' AND user_id = '".$GLOBALS['user_id']."' LIMIT 1";
            mysqli_query($GLOBALS['db']['link'], $q);
        }
        
        return $ret;
        
    }
    
    function badgesEarnedList($user_id=''){
        
        // Get all badges earned
        // return array
        
        if(!$user_id && $user_id != $GLOBALS['user_id']) return false;
        
        $query = "SELECT * FROM badges_earned LEFT JOIN badges USING (badge_id) WHERE user_id = '".mysqli_real_escape_string($GLOBALS['db']['link'], $user_id)."' ORDER BY datetime";
        $res   = mysqli_query($GLOBALS['db']['link'], $query);
        if(!mysqli_num_rows($res)) return false;
        while($row = mysqli_fetch_assoc($res)) $rows[] = $row;
        
        return $rows;
        
    }
    
    function collection($user_id, $usrname){
        
        //display a user's collection
        
        if(!$rows = $this->badgesEarnedList($user_id)) echo '<span class="none">'.$usrname.' hasn\'t earned any badges yet.</span>';
        else {
            $ret = '
            <ul class="badges">
                ';
                foreach($rows as $row){
                    $ret.= '<li><a href="/~'.$usrname.'/badges/'.$row['badge_id'].'/'.formatNameURL($row['name']).'" class="badge user-profile-nav"><img src="/bin/img/badges/'.$row['badge_id'].'.png" width="70" height="70" border="0" title="'.htmlSC($row['name']).'"/></a></li>';
                }
                $ret.= '
            </ul>
            <br style="clear:left;"/>
            ';
        }
        
        return $ret;
        
    }
    
    function show($badge_id, $user_id=''){
        
        //show a given badge
        //if $user_id, show when user earned it
        
        $this->get($badge_id);
        
        if($user_id){
            $q = "SELECT * FROM badges_earned WHERE badge_id = '".mysqli_real_escape_string($GLOBALS['db']['link'], $badge_id)."' AND user_id = '".$user_id."' LIMIT 1";
            if(!$earneddat = mysqli_fetch_object(mysqli_query($GLOBALS['db']['link'], $q))) return false;
        }
        
        $num = mysqli_num_rows(mysqli_query($GLOBALS['db']['link'], "SELECT * FROM badges_earned WHERE badge_id='".mysqli_real_escape_string($GLOBALS['db']['link'], $badge_id)."';"));
        
        $ret = '
        <div class="showbadge badge">
            <div class="badgeimg"><img src="/bin/img/badges/'.$badge_id.'.png" alt="badge: '.$this->badges[$badge_id]['name'].'"/></div>
            <h6>'.$this->badges[$badge_id]['name'].'</h6>
            <big>'.bb2html($this->badges[$badge_id]['description']).'</big>
            '.($user_id ? '<div class="message">This <span class="value '.$this->badges[$badge_id]['value2'].'">'.$this->badges[$badge_id]['value2'].' badge</span> was earned by '.outputUser($user_id).' on '.formatDate($earneddat->datetime).'</div>' : '').'
            <small><span class="arrow-right">&nbsp;</span>&nbsp;&nbsp;<b>'.$num.'</b> '.($num != 1 ? 'people have' : 'person has').' earned this badge.</small>
        </div>
        <div class="clear"></div>';
        
        return $ret;
        
    }
}