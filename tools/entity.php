<?php

function entity_load($name, $id) {
  $id_field = $name[0] . 'id';
  $fields = function_exists("{$name}_fields") ? call_user_func_array("{$name}_fields", array()) : array();
  $entity = (object) reset(db_fetch('SELECT `' . $name . '`.' . $id_field . ' FROM `' . $name . '` WHERE `' . $name . '`.delete IS NULL AND `' . $name . '`.' . $id_field . ' = :' . $id_field . ' LIMIT 1', array(':' . $id_field => $id)));

  foreach ($fields as $field => $options) {
    $entity->$field = array();

    foreach (db_fetch('SELECT `data` FROM `field_' . $name . '_' . $field .'` WHERE `field_' . $name . '_' . $field . '`.' . $id_field . ' = :' . $id_field, array(':' . $id_field => $id)) as $value) {
      switch ($options['type']) {
        case 'serialize':
          $entity->{$field}[] = unserialize($value['data']);
          break;

        case 'bool':
          $entity->{$field}[] = (bool) $value['data'];
          break;

        default:
          $entity->{$field}[] = $value['data'];
          break;
      }
    }
  }

  $entity->entity_name = $name;

  return $entity;
}

function entity_delete($name, &$entity) {
  $entity->delete = TRUE;
  entity_save($name, $entity);
  $entity = NULL;
}

function entity_save($name, &$entity) {
  $id_field = $name[0] . 'id';
  $fields = function_exists("{$name}_fields") ? call_user_func_array("{$name}_fields", array()) : array();

  if (empty($entity->$id_field)) {
    db_prepere('INSERT INTO `' . $name . '` (`delete`) VALUES (NULL)');

    $entity->$id_field = mysql_insert_id();

    foreach ($fields as $field => $options) {
      if (!empty($entity->$field)) {
        $placeholders = array(
          ':' . $id_field => $entity->$id_field,
        );
        $sql = 'INSERT INTO `field_' . $name . '_' . $field . '` (`' . $id_field . '`, `data`) VALUES';

        foreach (array_values($entity->$field) as $key => $value) {
          switch ($options['type']) {
            case 'serialize':
              $placeholders[':data_' . $key] = serialize($value);
              break;

            default:
              $placeholders[':data_' . $key] = $value;
              break;
          }

          $sql .= ' (:' .  $id_field . ', :data_' . $key . '),';
        }

        db_prepere(rtrim($sql, ','), $placeholders);
      }
    }
  }
  else {
    if (!empty($entity->delete)) {
      db_prepere('UPDATE `' . $name . '` SET `delete` = :delete WHERE `' . $id_field . '` = :' . $id_field, array(
        ':' . $id_field => $entity->$id_field,
        ':delete' => $entity->delete ? 1 : 'NULL',
      ));
    }

    foreach ($fields as $field => $options) {
      if (isset($entity->$field)) {
        db_prepere('DELETE FROM `field_' . $name . '_' . $field . '` WHERE `' . $id_field . '` = :' . $id_field, array(
          ':' . $id_field => $entity->$id_field,
        ));

        if (!empty($entity->$field)) {
          $placeholders = array(
            ':' . $id_field => $entity->$id_field,
          );
          $sql = 'INSERT INTO `field_' . $name . '_' . $field . '` (`' . $id_field . '`, `data`) VALUES';

          foreach (array_values($entity->$field) as $key => $value) {
            switch ($options['type']) {
              case 'serialize':
                $placeholders[':data_' . $key] = serialize($value);
                break;

              default:
                $placeholders[':data_' . $key] = $value;
                break;
            }

            $sql .= ' (:' .  $id_field . ', :data_' . $key . '),';
          }

          db_prepere(rtrim($sql, ','), $placeholders);
        }
      }
    }
  }
}

function entity_create($name) {
  $id_field = $name[0] . 'id';
  $fields = function_exists("{$name}_fields") ? call_user_func_array("{$name}_fields", array()) : array();

  db_prepere('DROP TABLE IF EXISTS `' . $name . '`');
  db_prepere('CREATE TABLE `' . $name . '` (`' . $id_field . '` int NOT NULL AUTO_INCREMENT PRIMARY KEY, `delete` int DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8');

  foreach ($fields as $field => $options) {
    $sql_field = '';

    switch ($options['type']) {
      case 'text':
        $sql_field = 'varchar(255) CHARACTER SET utf8 DEFAULT NULL';
        break;

      case 'textarea':
        $sql_field = 'text CHARACTER SET utf8 DEFAULT NULL';
        break;

      case 'int':
        $sql_field = 'int DEFAULT NULL';
        break;

      case 'bool':
        $sql_field = 'tinyint DEFAULT NULL';
        break;

      case 'serialize':
        $sql_field = 'text CHARACTER SET utf8 DEFAULT NULL';
        break;
    }

    if (!empty($sql_field)) {
      db_prepere('DROP TABLE IF EXISTS `field_' . $name . '_' . $field . '`');
      db_prepere('CREATE TABLE `field_' . $name . '_' . $field . '` (`' . $id_field . '` int NOT NULL, `data` ' . $sql_field . ') ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }
  }
}
