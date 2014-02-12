<?php

namespace Forms\Controller;

class Api extends \Cockpit\Controller {


    public function find(){

        $filter = $this->param("filter", null);
        $limit  = $this->param("limit", null);
        $sort   = $this->param("sort", null);
        $skip   = $this->param("skip", null);

        $docs = $this->getCollection("common/forms")->find($filter);

        if($limit) $docs->limit($limit);
        if($sort)  $docs->sort($sort);
        if($skip)  $docs->sort($skip);

        $docs = $docs->toArray();

        if(count($docs) && $this->param("extended", false)){
            foreach ($docs as &$doc) {
                $doc["count"] = $this->app->module("forms")->collectionById($doc["_id"])->count();
            }
        }

        return json_encode($docs);
    }

    public function findOne(){

        $filter = $this->param("filter", null);
        $doc    = $this->getCollection("common/forms")->findOne($filter);

        return $doc ? json_encode($doc) : '{}';
    }


    public function save(){

        $form = $this->param("form", null);

        if($form) {

            $form["modified"] = time();
            $form["_uid"]     = @$this->user["_id"];

            if(!isset($form["_id"])){
                $form["created"] = $form["modified"];
            }

            $this->getCollection("common/forms")->save($form);
        }

        return $form ? json_encode($form) : '{}';
    }

    public function remove(){

        $form = $this->param("form", null);

        if($form) {
            $frm = "form".$form["_id"];

            $this->app->data->forms->dropcollection($frm);
            $this->getCollection("common/forms")->remove(["_id" => $form["_id"]]);
        }

        return $form ? '{"success":true}' : '{"success":false}';
    }


    public function entries() {

        $form = $this->param("form", null);
        $entries    = [];

        if($form) {

            $filter = $this->param("filter", null);
            $limit  = $this->param("limit", null);
            $sort   = $this->param("sort", null);
            $skip   = $this->param("skip", null);

            $docs = $this->app->module("forms")->collectionById($form["_id"])->find($filter);

            if($limit) $docs->limit($limit);
            if($sort)  $docs->sort($sort);
            if($skip)  $docs->sort($skip);

            $entries = $docs->toArray();

        }

        return json_encode($entries);
    }

    public function removeentry(){

        $form = $this->param("form", null);
        $entryId    = $this->param("entryId", null);

        if($form && $entryId) {

            $this->app->module("forms")->collectionById($form["_id"])->remove(["_id" => $entryId]);
        }

        return ($form && $entryId) ? '{"success":true}' : '{"success":false}';
    }

    public function saveentry(){

        $form = $this->param("form", null);
        $entry      = $this->param("entry", null);

        if($form && $entry) {

            $entry["modified"] = time();
            $entry["_uid"]     = @$this->user["_id"];

            if(!isset($entry["_id"])){
                $entry["created"] = $entry["modified"];
            }

            $this->app->module("forms")->collectionById($form["_id"])->save($entry);
        }

        return $entry ? json_encode($entry) : '{}';
    }

}