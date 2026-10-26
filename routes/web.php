<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminAdoptionController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminCatalogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCouponController;
use App\Http\Controllers\AdminLocationController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AdminStaffScheduleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingWizardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\FeedingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetHealthController;
use App\Http\Controllers\PetShareController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('/newsletter', NewsletterSubscriptionController::class)
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');

Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [CatalogController::class, 'show'])->name('products.show');
Route::get('/categories/{category:slug}', [CatalogController::class, 'category'])->name('categories.show');
Route::get('/brands/{brand:slug}', [CatalogController::class, 'brand'])->name('brands.show');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/adoption', [AdoptionController::class, 'index'])->name('adoption.index');
Route::get('/adoption/{listing}', [AdoptionController::class, 'show'])->name('adoption.show');
Route::get('/pet/{token}', [PetShareController::class, 'show'])->name('pets.public');
Route::get('/pet/{token}/qr', [PetShareController::class, 'qr'])->name('pets.qr');
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
Route::get('/payment/vnpay/ipn', [PaymentController::class, 'vnpayIpn'])->name('payment.vnpay.ipn');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/map', [MapController::class, 'index'])->name('map');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('/cart/items/{product}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::delete('/account/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
    Route::get('/account/pets', [PetController::class, 'index'])->name('account.pets');
    Route::post('/account/pets', [PetController::class, 'store'])->name('account.pets.store');
    Route::get('/account/pets/{pet}', [PetHealthController::class, 'show'])->name('account.pets.show');
    Route::post('/account/pets/{pet}/health', [PetHealthController::class, 'store'])->name('account.pets.health.store');
    Route::put('/account/pets/{pet}/health/{record}', [PetHealthController::class, 'update'])->name('account.pets.health.update');
    Route::delete('/account/pets/{pet}/health/{record}', [PetHealthController::class, 'destroy'])->name('account.pets.health.destroy');
    Route::post('/account/pets/{pet}/current', [PetController::class, 'setCurrent'])->name('account.pets.current');
    Route::delete('/account/pets/current', [PetController::class, 'clearCurrent'])->name('account.pets.current.clear');
    Route::delete('/account/pets/{pet}', [PetController::class, 'destroy'])->name('account.pets.destroy');
    Route::patch('/account/pets/{pet}/sharing', [PetShareController::class, 'updatePrivacy'])->name('account.pets.sharing.update');
    Route::get('/account/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/adoption/{listing}/apply', [AdoptionController::class, 'apply'])->name('adoption.apply');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/account/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/account/loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::get('/account/feeding', [FeedingController::class, 'index'])->name('feeding.index');
    Route::post('/account/feeding', [FeedingController::class, 'store'])->name('feeding.store');
    Route::post('/account/feeding/{estimate}/reorder', [FeedingController::class, 'reorder'])->name('feeding.reorder');
    Route::get('/account/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/account/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/account/orders/{order}/pay-again', [OrderController::class, 'payAgain'])->name('orders.pay-again');
    Route::get('/account/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/account/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/account/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('/account/bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
    Route::get('/book', [BookingWizardController::class, 'create'])->name('booking.create');
    Route::post('/book', [BookingWizardController::class, 'store'])->name('booking.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class)->except('show');
    Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::patch('products/{product}/images/{image}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.images.primary');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::patch('users/{user}/active', [AdminUserController::class, 'toggleActive'])->name('users.active');
    Route::get('{resource}', [AdminCatalogController::class, 'index'])->whereIn('resource', ['categories', 'brands', 'services'])->name('catalog.index');
    Route::get('{resource}/create', [AdminCatalogController::class, 'create'])->whereIn('resource', ['categories', 'brands', 'services'])->name('catalog.create');
    Route::post('{resource}', [AdminCatalogController::class, 'store'])->whereIn('resource', ['categories', 'brands', 'services'])->name('catalog.store');
    Route::get('{resource}/{id}/edit', [AdminCatalogController::class, 'edit'])->whereIn('resource', ['categories', 'brands', 'services'])->name('catalog.edit');
    Route::put('{resource}/{id}', [AdminCatalogController::class, 'update'])->whereIn('resource', ['categories', 'brands', 'services'])->name('catalog.update');
    Route::delete('{resource}/{id}', [AdminCatalogController::class, 'destroy'])->whereIn('resource', ['categories', 'brands', 'services'])->name('catalog.destroy');
    Route::get('categories', fn () => redirect()->route('admin.catalog.index', 'categories'))->name('categories.index');
    Route::get('brands', fn () => redirect()->route('admin.catalog.index', 'brands'))->name('brands.index');
    Route::get('services', fn () => redirect()->route('admin.catalog.index', 'services'))->name('services.index');
    Route::resource('coupons', AdminCouponController::class)->except('show');
    Route::get('adoption/listings', [AdminAdoptionController::class, 'listings'])->name('adoption.listings');
    Route::patch('adoption/listings/{listing}/status', [AdminAdoptionController::class, 'updateListing'])->name('adoption.listings.status');
    Route::get('adoption/applications', [AdminAdoptionController::class, 'applications'])->name('adoption.applications');
    Route::patch('adoption/applications/{application}/status', [AdminAdoptionController::class, 'updateApplication'])->name('adoption.applications.status');
    Route::resource('posts', AdminPostController::class)->except('show');
    Route::resource('locations', AdminLocationController::class)->except('show');
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/toggle', [AdminReviewController::class, 'toggle'])->name('reviews.toggle');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('staff/{staff}/schedule', [AdminStaffScheduleController::class, 'edit'])->name('staff.schedule.edit');
    Route::put('staff/{staff}/schedule', [AdminStaffScheduleController::class, 'update'])->name('staff.schedule.update');
});

Route::middleware(['auth', 'role:staff,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/mark-paid', [AdminOrderController::class, 'markCodPaid'])->name('orders.mark-paid');
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
});

if (app()->environment('local')) {
    Route::get('/design', [DesignController::class, 'index'])->name('design');
    Route::get('/design/admin', [DesignController::class, 'admin'])->name('design.admin');
    Route::view('/email-preview', 'emails.preview')->name('email-preview');
}

require __DIR__.'/auth.php';
