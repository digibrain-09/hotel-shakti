<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Notification ID is UUID by default
            $table->string('type'); // e.g. App\Notifications\NewOrderNotification
            $table->morphs('notifiable'); // notifiable_id & notifiable_type (user model, etc.)
            $table->text('data'); // JSON payload of the notification
            $table->timestamp('read_at')->nullable(); // When notification was read
            $table->timestamps(); // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
