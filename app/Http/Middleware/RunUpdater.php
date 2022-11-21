<?php

    namespace App\Http\Middleware;

    use App\User;
    use Closure;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Schema\Builder;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Modules\Booking\Models\Booking;
    use Modules\Core\Models\Settings;
    use Modules\Media\Models\MediaFile;
    use Modules\Page\Models\Page;
    use Modules\Property\Models\PropertyCategory;
    use Modules\Review\Models\Review;
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    use Modules\Property\Models\Property;

    class RunUpdater
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
            if (strpos($request->path(), 'install') === false && file_exists(storage_path().'/installed')) {
                $this->runMigration();
                $this->runMigrationTo120();
                $this->runMigrationTo130();
            }
            return $next($request);
        }

        protected function runMigration()
        {

            if (!Schema::hasTable('bravo_property_category_translations')) {
                Schema::create('bravo_property_category_translations', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->integer('origin_id')->unsigned();
                    $table->string('locale')->index();

                    $table->string('name', 255)->nullable();
                    $table->text('content')->nullable();

                    $table->bigInteger('create_user')->nullable();
                    $table->bigInteger('update_user')->nullable();

                    $table->unique(['origin_id', 'locale']);

                    $table->softDeletes();
                    $table->timestamps();
                });
            }

            $version = '1.1.2';
            if (version_compare(setting_item('db_schema_version'), $version, '>=')) {
                return;
            }


            if (!Schema::hasTable('bravo_agencies_translations')) {
                Schema::create('bravo_agencies_translations', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->integer('origin_id')->unsigned();
                    $table->string('locale')->index();

                    $table->string('name', 255)->nullable();
                    $table->text('content')->nullable();

                    $table->bigInteger('create_user')->nullable();
                    $table->bigInteger('update_user')->nullable();

                    $table->unique(['origin_id', 'locale']);

                    $table->softDeletes();
                    $table->timestamps();
                });
            }

            $agent = Role::findOrCreate('agent');

            $agent->givePermissionTo('dashboard_agent_access');

            setting_update_item('db_schema_version', $version);
        }

        protected function runMigrationTo120()
        {
            $version = '1.2.0';
            if (version_compare(setting_item('db_schema_version'), $version, '>=')) {
                return;
            }
            Artisan::call('migrate', [
                '--force' => true,
            ]);
            // Code migrate in here

            setting_update_item('db_schema_version', $version);
        }

        protected function runMigrationTo130()
        {

            $version = '1.3.6';
            if (version_compare(setting_item('db_schema_version'), $version, '>=')) {
                return;
            }
            Artisan::call('migrate', [
                '--force' => true,
            ]);
            if (empty(MediaFile::findMediaByName("error_404_banner")->id)) {
                DB::table('media_files')->insert([
                    ['file_name' => 'error_404_banner', 'file_path' => 'demo/general/404.png', 'file_type' => 'image/png', 'file_extension' => 'png'],
                ]);
                setting_update_item('error_404_banner',MediaFile::findMediaByName("error_404_banner")->id);
                setting_update_item('error_404_title','Ohh! Page Not Found');
                setting_update_item('error_404_desc','We can’t seem to find the page you’re looking for');
            }
            if(!Schema::hasColumn(Page::getTableName(),'header_style')){
                Schema::table(Page::getTableName(),function ($builder){
                    $builder->string('header_style',255)->nullable();
                });
            }
            if(!Schema::hasColumn(Page::getTableName(),'body_width')){
                Schema::table(Page::getTableName(),function ($builder){
                    $builder->string('body_width',255)->nullable();
                });
            }

            if(!Schema::hasColumn(PropertyCategory::getTableName(),'icon')){
                Schema::table(PropertyCategory::getTableName(),function ($builder){
                    $builder->string('icon',255)->nullable();
                });

            }
            PropertyCategory::where('name','Apartment')->update(['icon'=>'flaticon-building']);
            PropertyCategory::where('name','Condo')->update(['icon'=>'flaticon-house-2']);
            PropertyCategory::where('name','Family House')->update(['icon'=>'flaticon-house-1']);
            PropertyCategory::where('name','Modern Villa')->update(['icon'=>'flaticon-house']);
            PropertyCategory::where('name','Town House')->update(['icon'=>'flaticon-house-2']);

            setting_update_item('db_schema_version', $version);
        }

    }
