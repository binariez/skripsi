<?php
require_once __DIR__ . "/Connection.php";

class Session
{
    public function __construct(public string $uname, public string $role,  public bool $isLogin) {}
}

class NSessionHandler
{
    public static function cekAuth($uname, $pwd)
    {
        global $db;
        $filter = ['user_uname' => $uname, 'user_pwd' => $pwd];
        $result = $db->user->findOne($filter);

        if ($result)
            return true;
        else
            return false;
    }

    public static function gantiPwd($uname, $pwd, $pwdbaru)
    {
        global $db;
        $filter = ['user_uname' => $uname, 'user_pwd' => $pwd];
        $options = ['$set' => ['user_pwd' => $pwdbaru]];
        if ($db->user->countDocuments($filter) > 0) {
            if ($db->user->updateOne($filter, $options)) {
                return true;
            }
        }
        return false;
    }

    public static function getDataU($uname)
    {
        global $db;
        $filter = ['user_uname' => $uname];
        $result = $db->user->find($filter);
        foreach ($result as $r) {
            $role = $r['user_role'];
            $return[] = [
                'id' => $r['_id'],
                'uname' => $r['user_uname'],
                'nama' => $r['user_nama'],
                'email' => $role == 'PLG' ? $r['plg_email'] : '',
                'alamat' => $role == 'PLG' ? $r['plg_alamat'] : '',
                'role' => $role,
                'pfp' => $r['user_pfp'],
            ];
        }
        return $return;
    }

    public static function getProdukAll($filter, $options)
    {
        global $db;
        $produk = $db->produk->find($filter, $options);
        $return = iterator_to_array($produk);

        return $return;
    }

    public static function getProdukOne($id)
    {
        try {
            global $db;
            $hasil = $db->produk->findOne(["_id" => new MongoDB\BSON\ObjectId($id)]);
            return $hasil;
        } catch (Exception $err) {
            echo "terjadi kesalahan: " . $err->getMessage();
            return null;
        }
    }

    public static function hitungRating($prod_id)
    {
        global $db;
        $prod_id = new MongoDB\BSON\ObjectId($prod_id);

        $pipeline = [
            ['$match' => ['id_prod' => $prod_id]],
            [
                '$group' => [
                    '_id' => '$id_prod',
                    'average_rating' => ['$avg' => '$review_rating'],
                    'total_reviews' => ['$sum' => 1]
                ]
            ]
        ];

        $result = $db->review->aggregate($pipeline)->toArray();

        if (count($result) > 0) {
            $avg = $result[0]['average_rating'];
            $count = $result[0]['total_reviews'];
            // echo "Rating rata-rata: " . round($avg, 2) . " dari " . $count . " review";
            $return = round($avg, 2);
        } else {
            $return = 0;
        }
        return $return;
    }

    public static function setLogin($id, $nama, $uname, $email, $alamat, $role, $pfp)
    {
        if (!isset($_SESSION['UserLogin'])) {
            $_SESSION['UserLogin'] = [];
        }

        $userdata = [
            "id" => $id,
            "nama" => $nama,
            "uname" => $uname,
            "email" => $role == 'PLG' ? $email : '',
            "alamat" => $role == 'PLG' ? $alamat : '',
            "pfp" => $pfp,
            "role" => $role,
        ];

        $_SESSION['UserLogin'][] = $userdata;

        return true;
    }

    public static function logout()
    {
        session_start();
        session_destroy();
    }
}
