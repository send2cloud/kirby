<?php

/**
 * Role
 *
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
abstract class RoleAbstract {

  protected $id          = null;
  protected $name        = null;
  protected $panel       = false;
  protected $permissions = array(
      'panel.access'         => true,
      'panel.page.create'    => true,
      'panel.page.update'    => true,
      'panel.page.delete'    => true,
      'panel.page.sort'      => true,
      'panel.page.hide'      => true,
      'panel.page.move'      => true,
      'panel.site.update'    => true,
      'panel.file.upload'    => true,
      'panel.file.replace'   => true,
      'panel.file.rename'    => true,
      'panel.file.update'    => true,
      'panel.file.sort'      => true,
      'panel.file.delete'    => true,
      'panel.user.create'    => true,
      'panel.user.update'    => true,
      'panel.user.delete'    => true,
      'panel.avatar.upload'  => true,
      'panel.avatar.delete'  => true,
    );

  public $default = false;

  public function __construct($data = array()) {

    if(!isset($data['id']))   throw new Exception('The role id is missing');
    if(!isset($data['name'])) throw new Exception('The role name is missing');

    // required data
    $this->id   = $data['id'];
    $this->name = $data['name'];

    if(isset($data['permissions']) and is_array($data['permissions'])) {
      $this->permissions = a::merge($this->permissions, $data['permissions']);
    } else if(isset($data['permissions']) and $data['permissions'] === false) {
      $this->permissions = array_fill_keys(array_keys($this->permissions), false);
    } else {
      $this->permissions = $this->permissions;
    }

    // fallback permissions support for old 'panel' role variable
    if(isset($data['panel']) and is_bool($data['panel'])) {
      $this->permissions['panel.access'] = $data['panel'];
    }

    // is this role the default role?
    if(isset($data['default'])) {
      $this->default = $data['default'] === true;
    }

  }

  public function id() {
    return $this->id;
  }

  public function name() {
    return $this->name;
  }

  // support for old 'panel' role permission
  public function hasPanelAccess() {
    return $this->hasPermission('panel.access');
  }

  public function hasPermission($target) {
    if($this->id == 'admin') {
      return true;
    } else if(isset($this->permissions[$target]) and $this->permissions[$target] === true) {
      return true;
    } else {
      return false;
    }
  }

  public function isDefault() {
    return $this->default;
  }

  public function users() {
    return kirby::instance()->site()->users()->filterBy('role', $this->id);
  }

  public function __toString() {
    return (string)$this->id;
  }

  /**
   * Converts the object data to an array
   * 
   * @return array
   */
  public function toArray() {
    return [
      'id'             => $this->id(),
      'name'           => $this->name(),
      'isDefault'      => $this->isDefault(),
      'hasPanelAccess' => $this->hasPanelAccess(),
    ];    
  }

  /**
   * Improved var_dump() output
   * 
   * @return array
   */
  public function __debuginfo() {
    return array_merge($this->toArray(), [
      'users' => $this->users()
    ]);
  }

}