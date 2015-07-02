<?php

class Encryption{
   public function encrypt($data)
   {
      return sha1(uniqid(mt_rand())) . base64_encode(serialize($data)) . sha1(uniqid(mt_rand()));
   }

   public function decrypt($data)
   {
      if(!$data) return;
      $pos =  strpos($data , substr($data , -40));
      $data =  substr($data , 40 , $pos);
      return unserialize(base64_decode($data));
   }
}