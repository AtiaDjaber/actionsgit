<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::group(
    ['prefix' => 'faculty'],
    function () {
        Route::get('/{faculty:reference}', [\App\Http\Controllers\ArticleStatusController::class, 'index']);
        Route::get('/{faculty:reference}/information', [\App\Http\Controllers\FacultyInformationController::class, 'index']);
        Route::get('/{faculty:reference}/post/{article:slug}', [\App\Http\Controllers\DepartmentClientController::class, 'getById']);
        Route::get('/{faculty:reference}/main', [\App\Http\Controllers\DepartmentClientController::class, 'index']);
        Route::get('/{faculty:reference}/category/{id}', [\App\Http\Controllers\ArticleStatusController::class, 'getByCategory']);
        Route::get('/{faculty:reference}/link', [\App\Http\Controllers\ArticleStatusController::class, 'getLinks']);
        Route::get('/{faculty:reference}/document', [\App\Http\Controllers\ArticleStatusController::class, 'getDocuments']);
        Route::get('/{faculty:reference}/schedules', [\App\Http\Controllers\ScheduleClientController::class, 'index']);
        Route::get('/{faculty:reference}/sections', [\App\Http\Controllers\SectionsClientController::class, 'index']);
        Route::get('/{faculty:reference}/corrections', [\App\Http\Controllers\DocumentSubjectClientController::class, 'index']);
        Route::get('/{faculty:reference}/notes', [\App\Http\Controllers\DocumentSubjectClientController::class, 'index']);
        Route::get('/{faculty:reference}/laboratories', [\App\Http\Controllers\LaboratoryClientController::class, 'index']);
        Route::get('/{faculty:reference}/laboratory/{id}', [\App\Http\Controllers\LaboratoryClientController::class, 'getById']);
        Route::get('/{faculty:reference}/department/{department:reference}/speciality/{speciality:id}', [\App\Http\Controllers\SubjectSpecialityController::class, 'index']);
    }
);

Route::get('sitemap', function () {
    // SitemapGenerator::create('http://192.168.1.103:8000/sitemap')
    //     ->configureCrawler(function (Crawler $crawler) {
    //         $crawler->setMaximumDepth(3);
    //     })
    //     ->writeToFile('sitemap.xml');

    return 'Ok';
});
Route::get('/', [\App\Http\Controllers\ArticleStatusController::class, 'index']);


Route::get('attachment/{filename}', [\App\Http\Controllers\AttachmentController::class, 'downloadAttachment'])->name("attachment");

Route::get('faculty/{faculty:reference}/department/{department:reference}', [\App\Http\Controllers\DepartmentClientController::class, 'index']);


Route::get('getById/{id}', function ($id) {
    $course = App\Models\Department::where('faculty_id', $id)->get();
    return response()->json($course);
});

Route::get('/ping', function () {
    return redirect('/sitemap.xml');
});

Route::group([
    'prefix' => 'admin'
], function () {
    Route::get('login', [\App\Http\Controllers\Admin\LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [\App\Http\Controllers\Admin\LoginController::class, 'login'])->name('admin.login');
    Route::post('logout', [\App\Http\Controllers\Admin\LoginController::class, 'logout'])->name('admin.logout');

    // require isAdmin middleware
    Route::group([
        'middleware' => \App\Http\Middleware\Admin::class
    ], function () {
        Route::get('/', [\App\Http\Controllers\Admin\HomeController::class, 'index'])->name('admin.home');
        Route::post('/upload', [\App\Http\Controllers\Admin\ArticleController::class, 'uploadAttachments'])->name('admin.upload');

        Route::group(['prefix' => 'documents'], function () {
            Route::get('create', [\App\Http\Controllers\Admin\ArticleController::class, 'createDocument'])->name('admin.documents.create');
            Route::get('{article}/edit', [\App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('admin.documents.edit');
        });

        Route::group(['prefix' => 'analytic'], function () {
            Route::get('create', [\App\Http\Controllers\Admin\ArticleController::class, 'createAnalytic'])->name('admin.analytics.create');
            Route::get('{article}/edit', [\App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('admin.analytics.edit');
        });

        Route::group(['prefix' => 'links'], function () {
            Route::get('create', [\App\Http\Controllers\Admin\ArticleController::class, 'createLink'])->name('admin.links.create');
            Route::get('{article}/edit', [\App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('admin.links.edit');
        });

        Route::group(['prefix' => 'articles'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('admin.articles.index');
            Route::group(['middleware' => ['can:create article']], function () {
                Route::get('create', [\App\Http\Controllers\Admin\ArticleController::class, 'create'])->name('admin.articles.create');
            });

            Route::post('/', [\App\Http\Controllers\Admin\ArticleController::class, 'store'])->name('admin.articles.store');
            Route::post('{article}', [\App\Http\Controllers\Admin\ArticleController::class, 'update'])->name('admin.articles.update');
            Route::get('{article}/edit', [\App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('admin.articles.edit');
            Route::group(['middleware' => ['can:delete article']], function () {
                Route::delete('delete/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('admin.articles.destroy');
            });
        });

        Route::group(['prefix' => 'images'], function () {
            Route::delete('delete/{id}', [\App\Http\Controllers\Admin\ImageController::class, 'destroy'])
                ->name('admin.images.destroy');
        });

        Route::group(['prefix' => 'years'], function () {
            Route::post('/', [\App\Http\Controllers\Admin\YearController::class, 'store'])->name('admin.years.store');
        });
        Route::group(['prefix' => 'attachments'], function () {
            Route::delete('delete/{id}', [\App\Http\Controllers\Admin\AttachmentController::class, 'destroy'])
                ->name('admin.attachments.destroy');
        });

        Route::group(['prefix' => 'faculties'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\FacultyController::class, 'index'])->name('admin.faculties.index');
            Route::get('create', [\App\Http\Controllers\Admin\FacultyController::class, 'create'])->name('admin.faculties.create');
            Route::post('/', [\App\Http\Controllers\Admin\FacultyController::class, 'store'])->name('admin.faculties.store');
            Route::get('{faculty}/edit', [\App\Http\Controllers\Admin\FacultyController::class, 'edit'])->name('admin.faculties.edit');
            Route::put('{faculty}', [\App\Http\Controllers\Admin\FacultyController::class, 'update'])->name('admin.faculties.update');
            Route::group(['middleware' => ['can:delete faculty']], function () {
                Route::delete('{faculty}', [\App\Http\Controllers\Admin\FacultyController::class, 'destroy'])->name('admin.faculties.destroy');
            });
        });

        Route::group(['prefix' => 'laboratories'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\LaboratoryController::class, 'index'])->name('admin.laboratories.index');
            Route::get('create', [\App\Http\Controllers\Admin\LaboratoryController::class, 'create'])->name('admin.laboratories.create');
            Route::post('/', [\App\Http\Controllers\Admin\LaboratoryController::class, 'store'])->name('admin.laboratories.store');
            Route::get('{laboratory}/edit', [\App\Http\Controllers\Admin\LaboratoryController::class, 'edit'])->name('admin.laboratories.edit');
            Route::put('{laboratory}', [\App\Http\Controllers\Admin\LaboratoryController::class, 'update'])->name('admin.laboratories.update');
            Route::delete('{laboratory}', [\App\Http\Controllers\Admin\LaboratoryController::class, 'destroy'])->name('admin.laboratories.destroy');
        });

        Route::group(['prefix' => 'schedules'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\ScheduleAdminController::class, 'index'])->name('admin.schedules.index');
            Route::post('create', [\App\Http\Controllers\Admin\ScheduleAdminController::class, 'store'])->name('admin.schedules.create');
            Route::post('update', [\App\Http\Controllers\Admin\ScheduleAdminController::class, 'update'])->name('admin.schedules.update');
            Route::post('{schedule}/file', [\App\Http\Controllers\Admin\ScheduleAdminController::class, 'update'])->name('admin.schedules.edit');
            Route::delete('delete/{id}', [\App\Http\Controllers\Admin\ScheduleAdminController::class, 'destroy'])->name('admin.schedules.destroy');
        });

        Route::group(['prefix' => 'sections'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\SectionController::class, 'index'])->name('admin.sections.index');
            Route::post('create', [\App\Http\Controllers\Admin\SectionController::class, 'store'])->name('admin.sections.create');
            Route::post('update', [\App\Http\Controllers\Admin\SectionController::class, 'update'])->name('admin.sections.update');
            Route::post('{schedule}/file', [\App\Http\Controllers\Admin\SectionController::class, 'update'])->name('admin.sections.edit');
            Route::delete('delete/{id}', [\App\Http\Controllers\Admin\SectionController::class, 'destroy'])->name('admin.sections.destroy');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
            Route::get('create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
            Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
            Route::get('{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
            Route::put('{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
            Route::post('reset', [\App\Http\Controllers\Admin\UserController::class, 'reset'])->name('admin.users.reset');

            Route::group(['middleware' => ['role:admin']], function () {
                Route::post('admin_reset/{id}', [\App\Http\Controllers\Admin\UserController::class, 'admin_reset'])->name('admin.reset');
                Route::delete('delete/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
            });
        });

        Route::group(['prefix' => 'departments'], function () {
            Route::get('/getById/{id}', [\App\Http\Controllers\Admin\DepartmentController::class, 'getById']);
            Route::get('/', [\App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('admin.departments.index');
            Route::get('create', [\App\Http\Controllers\Admin\DepartmentController::class, 'create'])->name('admin.departments.create');
            Route::post('/', [\App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('admin.departments.store');
            Route::get('{department}/edit', [\App\Http\Controllers\Admin\DepartmentController::class, 'edit'])->name('admin.departments.edit');
            Route::put('{department}', [\App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('admin.departments.update');
            Route::delete('{department}', [\App\Http\Controllers\Admin\DepartmentController::class, 'destroy'])->name('admin.departments.destroy');
        });

        Route::group(['prefix' => 'subjects'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('admin.subjects.index');
            Route::get('create', [\App\Http\Controllers\Admin\SubjectController::class, 'create'])->name('admin.subjects.create');
            Route::post('/', [\App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('admin.subjects.store');
            Route::get('{subject}/edit', [\App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('admin.subjects.edit');
            Route::put('{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('admin.subjects.update');
            Route::delete('{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'destroy'])->name('admin.subjects.destroy');
        });

        Route::group(['prefix' => 'document_subjects'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\DocumentSubjectController::class, 'index'])->name('admin.document_subjects.index');
            Route::post('create', [\App\Http\Controllers\Admin\DocumentSubjectController::class, 'store'])->name('admin.document_subjects.create');
            Route::post('update/', [\App\Http\Controllers\Admin\DocumentSubjectController::class, 'update'])->name('admin.document_subjects.store');
            Route::post('{document_subject}/file', [\App\Http\Controllers\Admin\DocumentSubjectController::class, 'update'])->name('admin.schedules.edit');
            Route::delete('delete/{id}', [\App\Http\Controllers\Admin\DocumentSubjectController::class, 'destroy'])->name('admin.document_subjects.destroy');
        });

        Route::get('level_specialities/getById/{id}', [\App\Http\Controllers\Admin\SubjectController::class, 'getById']);

        Route::group(['prefix' => 'filieres'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\FiliereController::class, 'index'])->name('admin.filieres.index');
            Route::get('create', [\App\Http\Controllers\Admin\FiliereController::class, 'create'])->name('admin.filieres.create');
            Route::post('/', [\App\Http\Controllers\Admin\FiliereController::class, 'store'])->name('admin.filieres.store');
            Route::get('{filiere}/edit', [\App\Http\Controllers\Admin\FiliereController::class, 'edit'])->name('admin.filieres.edit');
            Route::put('{filiere}', [\App\Http\Controllers\Admin\FiliereController::class, 'update'])->name('admin.filieres.update');
            Route::post('delete/{id}', [\App\Http\Controllers\Admin\FiliereController::class, 'destroy'])->name('admin.filieres.destroy');
        });

        Route::group(['prefix' => 'specialties'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\SpecialityController::class, 'index'])->name('admin.specialties.index');
            Route::get('create', [\App\Http\Controllers\Admin\SpecialityController::class, 'create'])->name('admin.specialties.create');
            Route::post('/', [\App\Http\Controllers\Admin\SpecialityController::class, 'store'])->name('admin.specialties.store');
            Route::get('{speciality}/edit', [\App\Http\Controllers\Admin\SpecialityController::class, 'edit'])->name('admin.specialties.edit');
            Route::put('{speciality}', [\App\Http\Controllers\Admin\SpecialityController::class, 'update'])->name('admin.specialties.update');
            Route::delete('{speciality}', [\App\Http\Controllers\Admin\SpecialityController::class, 'destroy'])->name('admin.specialties.destroy');
        });
    });

    // makes sure that 404 error is not shown, if the previous middleware did not work(not logged in as admin)
    Route::get("{anything}", function () {
        abort(403);
    });
});
