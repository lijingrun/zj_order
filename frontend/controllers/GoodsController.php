<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/12
 * Time: 14:53
 */
namespace frontend\controllers;

use common\models\Customer_type;
use common\models\Goods;
use common\models\Member_price;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class GoodsController extends Controller{

    public function actionIndex(){
        $key_word = $_GET['key_word'];
        if(!empty($key_word)){
            $all_goods = Goods::find()->where("goods_name like '%".$key_word."%'")->orWhere("goods_sn like '%".$key_word."%'");
        }else{
            $all_goods = Goods::find();
        }
        $pages = new Pagination(['totalCount' => $all_goods->count(),'pageSize' => 10]);
        $goods = $all_goods->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $ranks = Customer_type::find()->asArray()->all();
        foreach($goods as $key=>$good):
            foreach($ranks as $rank):
                $price = Member_price::find()->where("goods_id =".$good['goods_id'])->andWhere("user_rank =".$rank['rank_id'])->asArray()->one();
                if(!empty($price)) {
                    $rank_array = array(
                        'rank_name' => $rank['rank_name'],
                        'price' => $price['user_price'],
                    );
                }else{
                    $rank_array = array(
                        'rank_name' => $rank['rank_name'],
                        'price' => $good['shop_price']*($rank['discount']/100),
                    );
                }
                $goods[$key]['price'][] = $rank_array;
            endforeach;
        endforeach;
        return $this->render('goods_list',[
            'goods' => $goods,
            'pages' => $pages,
        ]);
    }

}