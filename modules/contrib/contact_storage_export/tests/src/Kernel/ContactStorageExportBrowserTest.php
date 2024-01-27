<?php

namespace Drupal\Tests\contact_storage_export\Kernel;

use Drupal\contact\Entity\ContactForm;
use Drupal\contact\Entity\Message;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests contact storage export with batch.
 *
 * @group contact_storage
 */
class ContactStorageExportBrowserTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'contact_storage',
    'contact_storage_export',
    'csv_serialization',
    'contact',
    'user',
    'system',
    'file',
  ];

  /**
   * Tests contact storage export.
   *
   * @requires module contact_storage
   */
  public function testContactStorageExportBatch() {
    // Create a sample form.
    $contact_form_id = 'contact_storage_export_form';
    $contact_form = ContactForm::create(['id' => $contact_form_id]);
    $contact_form->save();

    // Create a sample message.
    $message = Message::create([
      'id' => 1,
      'contact_form' => $contact_form->id(),
      'name' => 'example',
      'mail' => 'admin@example.com',
      'created' => '1487321550',
      'ip_address' => '127.0.0.1',
      'subject' => 'Test subject',
      'message' => 'Test message',
    ]);
    $message->save();

    $account = $this->drupalCreateUser([
      'access administration pages',
      'access site-wide contact form',
      'administer contact forms',
      'export contact form messages',
    ]);
    $this->drupalLogin($account);

    $this->drupalGet('admin/structure/contact/manage/export', ['query' => ['contact_form' => $contact_form_id]]);
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm(['columns[id]' => 1, 'filename' => 'test.csv'], 'Export');
    $this->drupalGet("admin/structure/contact/manage/export-download/$contact_form_id/0");
    $this->assertSession()->pageTextContains(t('Your export is ready for download.'));
  }

}
