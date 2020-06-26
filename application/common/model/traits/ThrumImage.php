<?php
namespace app\admin\model\traits;
trait ThrumImage
{
    /**
     * 图片压缩宽高封装函数
     * @param $src_file
     * @param $des_w
     * @param $des_h
     */
    public function thrum($src_file,$des_w,$des_h){
        //封装一个图片处理函数（等比例缩放）
        // 传入的第一个参数为图片的地址，第二和第三个元素为目的图片的宽高
        error_reporting(E_ALL^E_NOTICE^E_WARNING);
        //获取图片的类型
        $srcarr = getimagesize($src_file);
        //处理图片创建函数和图片输出函数
        switch($srcarr[2]){
            case 1://gif
                $imagecreatefrom = 'imagecreatefromgif';
                $imageout = 'imagegif';
                break;
            case 2://jpg
                $imagecreatefrom = 'imagecreatefromjpeg';
                $imageout = 'imagejpeg';
                break;
            case 3://png
                $imagecreatefrom = 'imagecreatefrompng';
                $imageout = 'imagepng';
                break;
        }
        // 创建原图资源
        $src_img = $imagecreatefrom($src_file);
        //获取原图的宽高
        $src_w = imagesx($src_img);
        $src_h = imagesy($src_img);
        // 计算缩放比例（用原图片的宽高分别处以对应目的图片的宽高，选择比例大的作为基准进行缩放）
        $scale = ($src_w/$des_w)>($src_h/$des_h)?($src_w/$des_w):($src_h/$des_h);
        //计算实际缩放时目的图的宽高（向下取整）
        $des_w = floor($src_w/$scale);
        $des_h = floor($src_h/$scale);
        //创建画布
        $des_img = imagecreatetruecolor($des_w, $des_h);
        //设置缩放起点
        $des_x = 0;
        $des_y = 0;
        $src_x = 0;
        $src_y = 0;
        //缩放
        imagecopyresampled($des_img, $src_img, $des_x, $des_y, $src_x, $src_y, $des_w, $des_h, $src_w, $src_h);
        //输出图片
        //header('content-type:image/jpeg');
        //获取源文件的文件名
        $t_file = basename($src_file);
        // 获取源文件的路径名
        $t_dir = dirname($src_file);
        // 生成保存文件的文件路径名
        $s_file = $t_dir .'/'.$t_file;
        $imageout($des_img,$s_file);
    }
}

