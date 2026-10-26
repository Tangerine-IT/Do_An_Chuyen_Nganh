<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PetShareController extends Controller
{
    public function show(string $token): View
    {
        $pet = Pet::query()->where('public_token', $token)->with(['user', 'healthRecords'])->firstOrFail();

        return view('pets.public', [
            'pet' => $pet,
            'vaccinationCount' => $pet->public_vaccinations
                ? $pet->healthRecords->filter(fn ($record) => $record->type->value === 'vaccination')->count()
                : null,
        ]);
    }

    public function qr(string $token): Response
    {
        abort_unless(Pet::query()->where('public_token', $token)->exists(), 404);
        $url = route('pets.public', $token);
        $renderer = new ImageRenderer(new RendererStyle(280), new SvgImageBackEnd);
        $svg = (new Writer($renderer))->writeString($url);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }

    public function updatePrivacy(Request $request, Pet $pet): RedirectResponse
    {
        Gate::authorize('update', $pet);
        $data = $request->validate([
            'public_name' => ['boolean'],
            'public_photo' => ['boolean'],
            'public_vaccinations' => ['boolean'],
            'public_contact' => ['boolean'],
        ]);
        $pet->update([
            'public_token' => $pet->public_token ?: Str::random(48),
            'public_name' => (bool) ($data['public_name'] ?? false),
            'public_photo' => (bool) ($data['public_photo'] ?? false),
            'public_vaccinations' => (bool) ($data['public_vaccinations'] ?? false),
            'public_contact' => (bool) ($data['public_contact'] ?? false),
        ]);

        return back()->with('success', __('ui.account.share_saved'));
    }
}
