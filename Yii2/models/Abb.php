<?php

namespace app\models;

//TODO: 1. сделать фильтр при обновлении страницы
//TODO: 2. кнопка уточнить(или изменить) со статусом appruved = 2

use Yii;

/**
 * This is the model class for table "abb".
 *
 * @property integer $id
 * @property string $abbr
 * @property string $decode
 * @property string $description
 * @property integer $approved
 */
    class Abb extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'abb';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['abbr'], 'required'],
                [['description'], 'string'],
                [['approved'], 'integer'],
                [['abbr'], 'string', 'max' => 128],
                [['decode'], 'string', 'max' => 255],
                [[], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => '6LfXxCgTAAAAAOROVvAPOKq7QHYL-xaRBJfrSu9o']
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id' => Yii::t('app', 'ID'),
                'abbr' => Yii::t('app', 'Abbr'),
                'decode' => Yii::t('app', 'Decode'),
                'description' => Yii::t('app', 'Description'),
                'approved' => Yii::t('app', 'Approved'),
            ];
        }

        public function doSearch($q, $options, $limit, $page)
        {
            $result = self::find()
                ->where(['approved' => 1]);

            if ($options['strict']) {
                if ($options['abbronly']) {
                    $result->andWhere(['like', 'abbr', $q, false]);
                } else {
                    $result->andWhere([
                        'or',
                        ['like', 'abbr', $q, false],
                        ['like', 'decode', $q, false],
                        ['like', 'description', $q, false]
                    ]);
                }
            } else {
                if ($options['abbronly']) {
                    $result->andWhere(['like', 'abbr', $q]);
                } else {
                    $result->andWhere([
                        'or',
                        ['like', 'abbr', $q],
                        ['like', 'decode', $q],
                        ['like', 'description', $q]
                    ]);
                }
            }
            $result = $result
                ->offset($page * $limit)
                ->limit($limit)
                ->all();
            return $result;
        }
    }
