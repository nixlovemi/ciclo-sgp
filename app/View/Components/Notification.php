<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Notification extends Component
{
    public string $notificationClass = '';
    private const SESSION_NOT_TITLE = 'notification.title';
    private const SESSION_NOT_CONTENT = 'notification.content';
    private const SESSION_NOT_TYPE = 'notification.type';

    /**
     * Create a new component instance.
     *
     * @var string $type [info | light | dark | warning | sucess | primary]
     * @return void
     */
    public function __construct(
        public string $title = '',
        public string $content = '',
        public string $type = ''
    ) {
        $this->setUpSessionValues();
        $this->notificationClass = $this->getNotifClass();
    }

    private function setUpSessionValues(): void
    {
        if (empty($this->title)) {
            $this->title = session()->pull(Notification::SESSION_NOT_TITLE, '');
        }

        if (empty($this->content)) {
            $this->content = session()->pull(Notification::SESSION_NOT_CONTENT, '');
        }

        if (empty($this->type)) {
            $this->type = session()->pull(Notification::SESSION_NOT_TYPE, '');
        }
    }

    private function getNotifClass(): string
    {
        switch ($this->type) {
            case 'info':
                return 'alert-info';
                break;

            case 'dark':
                return 'alert-dark';
                break;

            case 'warning':
                return 'alert-warning';
                break;

            case 'success':
                return 'alert-success';
                break;

            case 'primary':
                return 'alert-primary';
                break;
            
            default:
                return 'alert-light';
                break;
        }
    }

    /**
     * Calling this will fill the session variables. After that just use the component's tag to display.
     */
    private static function setNotification(
        string $title,
        string $content,
        string $type = 'info'
    ): void {
        session([Notification::SESSION_NOT_TITLE => $title]);
        session([Notification::SESSION_NOT_CONTENT => $content]);
        session([Notification::SESSION_NOT_TYPE => $type]);
    }

    public static function setInfo(string $title, string $content): void
    {
        self::setNotification($title, $content, 'info');
    }

    public static function setLight(string $title, string $content): void
    {
        self::setNotification($title, $content, 'light');
    }

    public static function setDark(string $title, string $content): void
    {
        self::setNotification($title, $content, 'dark');
    }

    public static function setWarning(string $title, string $content): void
    {
        self::setNotification($title, $content, 'warning');
    }

    public static function setSuccess(string $title, string $content): void
    {
        self::setNotification($title, $content, 'success');
    }

    public static function setPrimary(string $title, string $content): void
    {
        self::setNotification($title, $content, 'primary');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if (empty($this->title) && empty($this->content)) {
            return;
        }

        return view('components.notification');
    }
}
