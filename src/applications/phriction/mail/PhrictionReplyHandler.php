<?php

final class PhrictionReplyHandler extends PhabricatorMailReplyHandler {

  public function validateMailReceiver($mail_receiver) {
    if (!($mail_receiver instanceof PhrictionDocument)) {
      throw new Exception('Mail receiver is not a PhrictionDocument!');
    }
  }

  public function getPrivateReplyHandlerEmailAddress(
    PhabricatorObjectHandle $handle) {
    return $this->getDefaultPrivateReplyHandlerEmailAddress(
      $handle,
      PhrictionDocumentPHIDType::TYPECONST);
  }

  public function getPublicReplyHandlerEmailAddress() {
    return $this->getDefaultPublicReplyHandlerEmailAddress(
      PhrictionDocumentPHIDType::TYPECONST);
  }

  protected function receiveEmail(PhabricatorMetaMTAReceivedMail $mail) {
    // TODO: Implement.
    return null;
  }

}
