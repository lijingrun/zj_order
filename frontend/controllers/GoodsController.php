<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/12
 * Time: 14:53
 */
namespace frontend\controllers;

use common\models\Category;
use common\models\Customer_type;
use common\models\Goods;
use common\models\Member_price;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class GoodsController extends Controller{

    public function actionIndex(){
        $key_word = $_GET['key_word'];
        $all_goods = Goods::find()->where("is_delete = 0");
        if(!empty($key_word)){
            $all_goods->where("goods_name like '%".$key_word."%'")->orWhere("goods_sn like '%".$key_word."%'");
        }
        $promote = $_GET['promote'];
        if(!empty($promote)){
            $all_goods->andWhere("is_promote =".$promote);
        }
        $cat_id = $_GET['cat_id'];
        if(!empty($cat_id)){
            $all_goods->andWhere("cat_id =".$cat_id);
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

    //产品详情
    public function actionDetail(){
        $id = $_GET['id'];
        $goods = Goods::find()->where("goods_id =".$id)->asArray()->one();
//        $member_price = Member_price::find()->where("goods_id =".$id)->asArray()->all();
        $ranks = Customer_type::find()->all();
        $member_price = array();
        foreach($ranks as $rank):
            $data = array();
            $data['rank_name'] = $rank['rank_name'];
            $m_price = Member_price::find()->where("goods_id =".$id)->andWhere("user_rank =".$rank['rank_id'])->asArray()->one();
            if(!empty($m_price)){
                $data['user_price'] = $m_price['user_price'];
            }else{
                $data['user_price'] = $goods['shop_price']*($rank['discount']/100);
            }
            $member_price[] = $data;
        endforeach;
        return $this->render("detail",[
            'goods' => $goods,
            'member_price' => $member_price,
        ]);
    }

    //产品分类
    public function actionCategory(){
        $info_data = Category::find()->asArray()->all();
        $child = array();
        $category = $this->cate($info_data,$child);
        return $this->render('category',[
            'category' => $category,
        ]);
    }



}