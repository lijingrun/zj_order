<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/10/15
 * Time: 16:48
 */

namespace frontend\controllers;

use common\models\Category;
use common\models\Customer_type;
use common\models\Goods;
use common\models\Member_price;
use common\models\Promotion;
use common\models\Promotion_goods;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class MarketingController extends Controller{
    public $enableCsrfValidation = false;

    public function actionIndex(){
        $all_data = Promotion::find();
        $pages = new Pagination(['totalCount' => $all_data->count(), 'pageSize' => '10']);
        $promotion = $all_data->offset($pages->offset)->limit($pages->limit)->all();
        $return_data = array();
        foreach($promotion as $key=>$val):
            $data = array();
            $data['promotion'] = $val;
            $data['rank'] = Customer_type::find()->where("rank_id in (".$val['rank'].")")->asArray()->all();
            $promotion_goods = Promotion_goods::find()->where("promotion_id =".$val['id'])->asArray()->all();
            $goods_ids = array();
            foreach($promotion_goods as $val):
                $goods_ids[] = $val['goods_id'];
            endforeach;
            $goods_ids = implode(",",$goods_ids);
            $goods = Goods::find()->where("goods_id in (".$goods_ids.")")->asArray()->all();
            $data['goods'] = $goods;
            $return_data[] = $data;
        endforeach;



        return $this->render('list',[
            'promotion' => $return_data,
            'pages' => $pages,
        ]);
    }

    public function actionAdd(){
        if(Yii::$app->request->post()){
            $start_time = strtotime($_POST['start_time']);
            $end_time = strtotime($_POST['end_time']);
            $type = $_POST['type'];  // 1为满送 2为满减 3为满折
            $number = $_POST['number'];
            $coefficient = $_POST['coefficient'];
            $rank = $_POST['rank'];  //目标数组
            $goods_id = $_POST['goods_id']; //活动商品数组
            $title = $_POST['title'];
            if(empty($rank)){
                Yii::$app->getSession()->setFlash('error','请选择客户等级！');
                return $this->redirect("index.php?r=marketing/add");
            }
            if(empty($goods_id)){
                Yii::$app->getSession()->setFlash('error','请选择活动产品！');
                return $this->redirect("index.php?r=marketing/add");
            }
            $rank = implode(',',$rank);
            $promotion = new Promotion();
            $promotion->title = $title;
            $promotion->start_time = $start_time;
            $promotion->end_time = $end_time;
            $promotion->type = $type;
            $promotion->number = $number;
            $promotion->coefficient = $coefficient;
            $promotion->rank = $rank;
            if($promotion->save()){
                $promotion_id = $promotion['id'];
                foreach($goods_id as $val):
                    $promotion_goods = new Promotion_goods();
                    $promotion_goods->goods_id = $val;
                    $promotion_goods->promotion_id = $promotion_id;
                    $promotion_goods->save();
                endforeach;
                Yii::$app->getSession()->setFlash('success','操作成功！');
                return $this->redirect('index.php?r=marketing');
            }else{
                Yii::$app->getSession()->setFlash('error','服务器繁忙，请稍后重试！');
                return $this->redirect("index.php?r=marketing/add");
            }

        }else{
            $rank = Customer_type::find()->asArray()->all();  //客户等级

            return $this->render("add",[
                'rank' => $rank,
            ]);
        }

    }

    public function actionFind_goods(){
        if(Yii::$app->request->post()){
            $goods_name = $_POST['goods_name'];
            $goods = Goods::find()->where("goods_name like '%".$goods_name."%'")->asArray()->all();
            if(!empty($goods)){
                foreach($goods as $good):
                    echo "<option value='".$good['goods_id']."' id='goods_detail".$good['goods_id']."'>".$good['goods_name']."</option>";
                endforeach;
            }
            exit;
        }
    }

    public function actionDel(){
        if(Yii::$app->request->post()){
            $id = $_POST['id'];
            if(Promotion::deleteAll("id =".$id) && Promotion_goods::deleteAll("promotion_id =".$id)){
                echo 111;
            }else{
                echo 222;
            }
            exit;
        }
    }

    public function actionEdit(){
        $id = $_GET['id'];
        if(Yii::$app->request->post()){
            $start_time = strtotime($_POST['start_time']);
            $end_time = strtotime($_POST['end_time']);
            $type = $_POST['type'];  // 1为满送 2为满减 3为满折
            $number = $_POST['number'];
            $coefficient = $_POST['coefficient'];
            $rank = $_POST['rank'];  //目标数组
            $goods_id = $_POST['goods_id']; //活动商品数组
            $title = $_POST['title'];
            if(empty($rank)){
                Yii::$app->getSession()->setFlash('error','请选择客户等级！');
                return $this->redirect("index.php?r=marketing/add");
            }
            if(empty($goods_id)){
                Yii::$app->getSession()->setFlash('error','请选择活动产品！');
                return $this->redirect("index.php?r=marketing/add");
            }
            $rank = implode(',',$rank);
            $promotion = Promotion::find($id)->one();
            $promotion->title = $title;
            $promotion->start_time = $start_time;
            $promotion->end_time = $end_time;
            $promotion->type = $type;
            $promotion->number = $number;
            $promotion->coefficient = $coefficient;
            $promotion->rank = $rank;
            if($promotion->save()){
                $promotion_id = $id;
                Promotion_goods::deleteAll("promotion_id =".$id);
                foreach($goods_id as $val):
                    $promotion_goods = new Promotion_goods();
                    $promotion_goods->goods_id = $val;
                    $promotion_goods->promotion_id = $promotion_id;
                    $promotion_goods->save();
                endforeach;
                Yii::$app->getSession()->setFlash('success','操作成功！');
                return $this->redirect('index.php?r=marketing');
            }else{
                Yii::$app->getSession()->setFlash('error','服务器繁忙，请稍后重试！');
                return $this->redirect("index.php?r=marketing/add");
            }
        }else {
            $promotion = Promotion::find($id)->asArray()->one();
            $choose_goods = Promotion_goods::find()->where("promotion_id =" . $id)->asArray()->all();
            $goods_ids = array();
            foreach ($choose_goods as $val) {
                $goods_ids[] = $val['goods_id'];
            }
            $ranks = Customer_type::find()->asArray()->all();
            $choose_rank = explode(',', $promotion['rank']);
            if (!empty($goods_ids)) {
                $goods_ids = implode(',', $goods_ids);
                $goods = Goods::find()->where("goods_id in(" . $goods_ids . ")")->asArray()->all();
            }


            return $this->render('edit', [
                'promotion' => $promotion,
                'rank' => $ranks,
                'goods' => $goods,
                'choose_rank' => $choose_rank,
            ]);
        }
    }

}
