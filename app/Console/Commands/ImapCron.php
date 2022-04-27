<?php

namespace App\Console\Commands;

use App\Logging\FileLog;
use App\Models\Api\V1\Supervisor\SupervisorNropmAnsweredMail;
use App\Models\Api\V1\Supervisor\SupervisorAnsweredMailAttachment;
use App\Models\Api\V1\Supervisor\SupervisorNropmCommand;
use Exception;
use Illuminate\Console\Command;
use Webklex\IMAP\Attachment;
use Webklex\IMAP\Client;
use Webklex\IMAP\Message;
use Webklex\IMAP\Support\AttachmentCollection;

class ImapCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imap:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cron which allows you to retrieve emails from a mailbox';

    private const MAILBOX_MAIN_FOLDER = 'INBOX';
    private const MAILBOX_DESTINATION_FOLDER = 'a traiter';

    private int $nropmAnsweredMailId;
    private int $nropmCommandId;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $mailboxClient = $this->getMailboxClient();
            $folder = $mailboxClient->getFolder(self::MAILBOX_MAIN_FOLDER);
            $unseenMails = $folder->messages()->unseen()->all()->get();

            foreach ($unseenMails as $unseenMail) {
                $actualUnseenMailSubject = $unseenMail->subject;

                $supervisorNropmCommand = SupervisorNropmCommand::where('mail_subject', '=', $actualUnseenMailSubject)->first();

                if (!$supervisorNropmCommand) {
                    $this->moveUnseenMailToMailboxFolder($unseenMail, self::MAILBOX_DESTINATION_FOLDER);

                    continue;
                }

                $this->nropmCommandId = $supervisorNropmCommand->supervisor_nropm_command_id;

                $actualUnseenMailContent = $unseenMail->getTextBody();
                $this->insertNropmAnsweredMail($actualUnseenMailSubject, $actualUnseenMailContent);

                if ($unseenMail->hasAttachments()) {
                    $this->insertAndUploadUnseenMailAttachments($unseenMail->getAttachments());
                }
            }
        } catch (Exception $e) {
            $logs = [
                'date' => date('Y/m/d H:i:s'),
                'source' => 'supervisornropmerror',
                'class' => explode("\\", get_class($this)),
                'logProjectFolder' => 'supervisor'
            ];

            FileLog::log('error', $logs, $e);
        }
    }

    private function getMailboxClient () {
        $oClient = new Client([
            'host'          => env('IMAP_HOST'),
            'port'          => env('IMAP_PORT'),
            'encryption'    => env('IMAP_ENCRYPTION'),
            'username'      => env('IMAP_USERNAME'),
            'password'      => env('IMAP_PASSWORD'),
            'protocol'      => env('IMAP_PROTOCOL')
        ]);

        $oClient->connect();

        return $oClient;
    }

    private function uploadAttachmentToLocalPath (Attachment $attachment, string $destinationFilePath) {
        $attachment->save(dirname($destinationFilePath), basename($destinationFilePath));
    }

    private function insertNropmAnsweredMail (string $mailSubject, string $mailContent) {
        $nropmAnsweredMail = new SupervisorNropmAnsweredMail();
        $nropmAnsweredMail->mail_subject = $mailSubject;
        $nropmAnsweredMail->mail_content = $mailContent;
        $nropmAnsweredMail->supervisor_nropm_command_id = $this->nropmCommandId;
        $nropmAnsweredMail->save();

        $this->nropmAnsweredMailId = $nropmAnsweredMail->supervisor_nropm_answered_mail_id;
    }

    private function insertAndUploadUnseenMailAttachments (AttachmentCollection $unseenMailAttachments) {
        foreach ($unseenMailAttachments as $unseenMailAttachment) {
            $attachmentFileName = $unseenMailAttachment->getName();
            $attachmentDestinationFilePath = storage_path() . '/app/uploads/' . $attachmentFileName;

            $this->uploadAttachmentToLocalPath($unseenMailAttachment, $attachmentDestinationFilePath);
            $this->insertNropmAnsweredMailAttachment($attachmentDestinationFilePath);
        }
    }

    private function insertNropmAnsweredMailAttachment (string $attachmentDestinationFilePath) {
        $nropmAnsweredMailAttachment = new SupervisorAnsweredMailAttachment();
        $nropmAnsweredMailAttachment->path = $attachmentDestinationFilePath;
        $nropmAnsweredMailAttachment->supervisor_nropm_answered_mail_id = $this->nropmAnsweredMailId;
        $nropmAnsweredMailAttachment->save();
    }

    private function moveUnseenMailToMailboxFolder (Message $unseenMail, string $folder) {
        $unseenMail->moveToFolder($folder);
    }
}
