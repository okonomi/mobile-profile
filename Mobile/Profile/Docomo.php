<?php
require_once 'Mobile/Profile/Abstract.php';


class Mobile_Profile_Docomo extends Mobile_Profile_Abstract
{
    protected function _correctProfile()
    {
        $result = $this->_getScrape('useragent');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device']);

            // 機種名
            $info->setDeviceID($row['device']);
            // モデル名
            $info->setModel($row['model']);

            // シリーズ
            $info->set('series', $row['series']);
            // 世代
            $info->set('generation', $row['generation']);
            // HTMLバージョン
            $info->set('browser', 'html', $row['version']);
            // キャッシュサイズ
            $info->set('browser', 'cache', $row['cache']);
        }

        $result = $this->_getScrape('series');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfoByProp('model', $row['model'], false);
            if (is_null($info)) {
                continue;
            }

            // シリーズ
            $info->set('series', $row['series']);
        }

        $result = $this->_getScrape('display');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device'], false);
            if (is_null($info)) {
                continue;
            }

            // 表示文字数
            $info->set('display', 'character', $row['character']);
            // 液晶画面領域(ブラウザ)
            $info->set('browser', 'screen', $row['browser']);
            // 液晶画面領域(待受画面)
            $info->set('display', 'screen', $row['display']);
            // 色
            $info->set('display', 'color', $row['color']);
        }

        $result = $this->_getScrape('appli');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device'], false);
            if (is_null($info)) {
                continue;
            }

            // プロファイル
            $info->set('appli', 'profile', $row['profile']);
            // アプリサイズ
            $info->set('appli', 'size', array(
                           'jar'        => $row['applisize']['jar'],
                           'scratchpad' => $row['applisize']['scratchpad'],
                       ));
            // 描画領域
            $info->set('appli', 'drawarea', array(
                           'panel'  => $row['drawarea']['panel'],
                           'canvas' => $row['drawarea']['canvas'],
                       ));
            // ヒープ
            $info->set('appli', 'heap', array(
                           'java'   => $row['heap']['java'],
                           'native' => $row['heap']['native'],
                           'widget' => $row['heap']['widget'],
                       ));
            // フォントサイズ
            $info->set('appli', 'font', $row['font']);
        }

        $result = $this->_getScrape('flash');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device'], false);
            if (is_null($info)) {
                continue;
            }

            // Flashバージョン
            $info->set('flash', 'flash', $row['version']);
            // 描画領域(ブラウザ)
            $info->set('flash', 'browser', $row['drawarea']['browser']);
            // 描画領域(待受画面)
            $info->set('flash', 'display', $row['drawarea']['display']);
            // ワークメモリ
            $info->set('flash', 'memory', $row['memory']);
            // フォント
            $info->set('flash', 'font', $row['font']);
            // スケーラブルフォントか
            $info->set('flash', 'scalable_font', $row['scalable_font']);
            // ポインティングデバイス対応
            $info->set('flash', 'pointing', $row['pointing']);
            // インライン再生
            $info->set('flash', 'inline', $row['inline']);
        }

        $result = $this->_getScrape('spec');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device'], false);
            if (is_null($info)) {
                continue;
            }

            // デコメバージョン
            $info->set('decomail', 'version', $row['decomail']['version']);
            // デコメ送信
            $info->set('decomail', 'send', $row['decomail']['send']);
            // デコメ編集
            $info->set('decomail', 'edit', $row['decomail']['edit']);
            // デコメテンプレート
            $info->set('decomail', 'template', array(
                           'allow' => $row['decomail']['template'],
                           'dl'    => $row['decomail']['template_dl'],
                           'title' => $row['decomail']['template_title'],
                       ));
        }

        $result = $this->_getScrape('imotion');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device'], false);
            if (is_null($info)) {
                continue;
            }

            // MobileMP4ンバージョン
            $info->set('imotion', 'version', $row['version']);
            // ファイルサイズ
            $info->set('imotion', 'filesize', $row['filesize']);
            // テキストテロップ対応
            $info->set('imotion', 'telop', $row['telop']);
            // 3Diモーション
            $info->set('imotion', '3d', $row['3d']);
        }

        $result = $this->_getScrape('photoframe');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device'], false);
            if (is_null($info)) {
                continue;
            }

            // フレームサイズ
            $info->set('photoframe', 'size', $row['size']);
        }

        $result = $this->_getScrape('menuicon');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device'], false);
            if (is_null($info)) {
                continue;
            }

            // メニューアイテム
            $info->set('menuicon', 'item', $row['item']);
            // アイコンサイズ
            $info->set('menuicon', 'icon_size', $row['icon_size']);
            // 背景サイズ
            $info->set('menuicon', 'background_size', $row['background_size']);
            // メニュー名称埋め込み
            $info->set('menuicon', 'need_embed', $row['need_embed']);
        }
    }

    private function _getScrape($name)
    {
        $name = ucfirst(strtolower($name));

        $filename  = 'Mobile/Profile/Docomo/'.$name.'.php';
        $classname = 'Mobile_Profile_Docomo_'.$name;

        require_once $filename;
        $component = new $classname();
        $result = $component->scrape();

        return $result;
    }
}
