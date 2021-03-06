<?php

final class AphrontFormView extends AphrontView {

  private $action;
  private $method = 'POST';
  private $header;
  private $data = array();
  private $encType;
  private $workflow;
  private $id;
  private $shaded = false;
  private $sigils = array();
  private $metadata;
  private $controls = array();
  private $fullWidth = false;

  public function setMetadata($metadata) {
    $this->metadata = $metadata;
    return $this;
  }

  public function getMetadata() {
    return $this->metadata;
  }

  public function setID($id) {
    $this->id = $id;
    return $this;
  }

  public function setAction($action) {
    $this->action = $action;
    return $this;
  }

  public function setMethod($method) {
    $this->method = $method;
    return $this;
  }

  public function setEncType($enc_type) {
    $this->encType = $enc_type;
    return $this;
  }

  public function setShaded($shaded) {
    $this->shaded = $shaded;
    return $this;
  }

  public function addHiddenInput($key, $value) {
    $this->data[$key] = $value;
    return $this;
  }

  public function setWorkflow($workflow) {
    $this->workflow = $workflow;
    return $this;
  }

  public function addSigil($sigil) {
    $this->sigils[] = $sigil;
    return $this;
  }

  public function setFullWidth($full_width) {
    $this->fullWidth = $full_width;
    return $this;
  }

  public function getFullWidth() {
    return $this->fullWidth;
  }

  public function appendInstructions($text) {
    return $this->appendChild(
      phutil_tag(
        'div',
        array(
          'class' => 'aphront-form-instructions',
        ),
        $text));
  }

  public function appendRemarkupInstructions($remarkup) {
    return $this->appendInstructions(
      PhabricatorMarkupEngine::renderOneObject(
        id(new PhabricatorMarkupOneOff())->setContent($remarkup),
        'default',
        $this->getUser()));
  }

  public function buildLayoutView() {
    return id(new PHUIFormLayoutView())
      ->setFullWidth($this->getFullWidth())
      ->appendChild($this->renderDataInputs())
      ->appendChild($this->renderChildren());
  }


  /**
   * Append a control to the form.
   *
   * This method behaves like @{method:appendChild}, but it only takes
   * controls. It will propagate some information from the form to the
   * control to simplify rendering.
   *
   * @param AphrontFormControl Control to append.
   * @return this
   */
  public function appendControl(AphrontFormControl $control) {
    $this->controls[] = $control;
    return $this->appendChild($control);
  }


  public function render() {
    require_celerity_resource('phui-form-view-css');

    foreach ($this->controls as $control) {
      $control->setUser($this->getUser());
    }

    $layout = $this->buildLayoutView();

    if (!$this->user) {
      throw new Exception(pht('You must pass the user to AphrontFormView.'));
    }

    $sigils = $this->sigils;
    if ($this->workflow) {
      $sigils[] = 'workflow';
    }

    return phabricator_form(
      $this->user,
      array(
        'class'   => $this->shaded ? 'phui-form-shaded' : null,
        'action'  => $this->action,
        'method'  => $this->method,
        'enctype' => $this->encType,
        'sigil'   => $sigils ? implode(' ', $sigils) : null,
        'meta'    => $this->metadata,
        'id'      => $this->id,
      ),
      $layout->render());
  }

  private function renderDataInputs() {
    $inputs = array();
    foreach ($this->data as $key => $value) {
      if ($value === null) {
        continue;
      }
      $inputs[] = phutil_tag(
        'input',
        array(
          'type'  => 'hidden',
          'name'  => $key,
          'value' => $value,
        ));
    }
    return $inputs;
  }

}
