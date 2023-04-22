<?php

declare(strict_types=1);

namespace Digitfab\Core;

if (!defined('ABSPATH')) {
    die;
}

class Activator
{
    public function activate(): void
    {
        add_role(
            'df_messages_manager',
            __('Messages manager', 'digitfab-core'),
            [
                "edit_message" => true,
                "read_message" => true,
                "delete_message" => true,
                "edit_messages" => true,
                "edit_others_messages" => true,
                "delete_messages" => true,
                "publish_messages" => true,
                "read_private_messages" => true,
                "delete_private_messages" => true,
                "delete_published_messages" => true,
                "delete_others_messages" => true,
                "edit_private_messages" => true,
                "edit_published_messages" => true,
                "read" => true,
            ]
        );

        add_role(
            'df_services_manager',
            __('Services manager', 'digitfab-core'),
            [
                "edit_service" => true,
                "read_service" => true,
                "delete_service" => true,
                "edit_services" => true,
                "edit_others_services" => true,
                "delete_services" => true,
                "publish_services" => true,
                "read_private_services" => true,
                "delete_private_services" => true,
                "delete_published_services" => true,
                "delete_others_services" => true,
                "edit_private_services" => true,
                "edit_published_services" => true,
                "read" => true,
            ]
        );

        $admin = get_role('administrator');

        $admin->add_cap("edit_message");
        $admin->add_cap("read_message");
        $admin->add_cap("delete_message");
        $admin->add_cap("edit_messages");
        $admin->add_cap("edit_others_messages");
        $admin->add_cap("delete_messages");
        $admin->add_cap("publish_messages");
        $admin->add_cap("read_private_messages");
        $admin->add_cap("delete_private_messages");
        $admin->add_cap("delete_published_messages");
        $admin->add_cap("delete_others_messages");
        $admin->add_cap("edit_private_messages");
        $admin->add_cap("edit_published_messages");

        $admin->add_cap("edit_service");
        $admin->add_cap("read_service");
        $admin->add_cap("delete_service");
        $admin->add_cap("edit_services");
        $admin->add_cap("edit_others_services");
        $admin->add_cap("delete_services");
        $admin->add_cap("publish_services");
        $admin->add_cap("read_private_services");
        $admin->add_cap("delete_private_services");
        $admin->add_cap("delete_published_services");
        $admin->add_cap("delete_others_services");
        $admin->add_cap("edit_private_services");
        $admin->add_cap("edit_published_services");
    }

    public function deactivate(): void
    {
        remove_role('df_contacts_manager');

        remove_role('df_messages_manager');

        $admin = get_role('administrator');
        
        $admin->remove_cap("edit_contact");
        $admin->remove_cap("read_contact");
        $admin->remove_cap("delete_contact");
        $admin->remove_cap("edit_contacts");
        $admin->remove_cap("edit_others_contacts");
        $admin->remove_cap("delete_contacts");
        $admin->remove_cap("publish_contacts");
        $admin->remove_cap("read_private_contacts");
        $admin->remove_cap("delete_private_contacts");
        $admin->remove_cap("delete_published_contacts");
        $admin->remove_cap("delete_others_contacts");
        $admin->remove_cap("edit_private_contacts");
        $admin->remove_cap("edit_published_contacts");
        $admin->remove_cap("edit_contacts");

        $admin->remove_cap("edit_message");
        $admin->remove_cap("read_message");
        $admin->remove_cap("delete_message");
        $admin->remove_cap("edit_messages");
        $admin->remove_cap("edit_others_messages");
        $admin->remove_cap("delete_messages");
        $admin->remove_cap("publish_messages");
        $admin->remove_cap("read_private_messages");
        $admin->remove_cap("delete_private_messages");
        $admin->remove_cap("delete_published_messages");
        $admin->remove_cap("delete_others_messages");
        $admin->remove_cap("edit_private_messages");
        $admin->remove_cap("edit_published_messages");
        $admin->remove_cap("edit_messages");

        $admin->remove_cap("edit_service");
        $admin->remove_cap("read_service");
        $admin->remove_cap("delete_service");
        $admin->remove_cap("edit_services");
        $admin->remove_cap("edit_others_services");
        $admin->remove_cap("delete_services");
        $admin->remove_cap("publish_services");
        $admin->remove_cap("read_private_services");
        $admin->remove_cap("delete_private_services");
        $admin->remove_cap("delete_published_services");
        $admin->remove_cap("delete_others_services");
        $admin->remove_cap("edit_private_services");
        $admin->remove_cap("edit_published_services");
        $admin->remove_cap("edit_services");
    }
}