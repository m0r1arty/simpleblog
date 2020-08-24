<?php

namespace app\models;

class BaseUsers extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
	public $authKey;
    /**
     * {@inheritdoc}
     */
    public static function findIdentity( $id )
    {
        return static::findOne( [ 'id' => $id ] );
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken( $token, $type = null )
    {
        return static::findOne( [ 'token' => $token ] );
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername( $username )
    {
        return static::findOne( [ 'login' => $username ] );
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey( $authKey )
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword( $password )
    {
        return $this->password === md5( $password );
    }


    public function beforeSave( $insert )
    {
        if( parent::beforeSave( $insert ) )
        {
            if( $this->isNewRecord )
            {
                $this->token = \Yii::$app->security->generateRandomString( 64 );
            }

            return true;
        }

        return false;
    }
}