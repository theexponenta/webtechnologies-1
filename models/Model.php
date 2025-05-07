<?php 


abstract class Model {
    public static abstract function fromRow(array $row): Model;
}
