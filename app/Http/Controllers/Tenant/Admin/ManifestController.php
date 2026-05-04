<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    /**
     * Generate PWA manifest.json dynamically
     */
    public function manifest(): JsonResponse
    {
        $appUrl = config('app.url');

        $manifest = [
            'name' => 'Sistem Desa Cibatu',
            'short_name' => 'Desa Cibatu',
            'description' => 'Sistem Informasi Desa Cibatu - Purwakarta',
            'start_url' => $appUrl . '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#1e40af',
            'orientation' => 'portrait-primary',
            'scope' => $appUrl . '/',
            'id' => 'desa-cibatu-pwa',
            'lang' => 'id',
            'dir' => 'ltr',
            'categories' => ['government', 'productivity', 'utilities'],
            'icons' => [
                [
                    'src' => '/images/icons/icon-72x72.png',
                    'sizes' => '72x72',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/images/icons/icon-96x96.png',
                    'sizes' => '96x96',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/images/icons/icon-128x128.png',
                    'sizes' => '128x128',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/images/icons/icon-144x144.png',
                    'sizes' => '144x144',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/images/icons/icon-152x152.png',
                    'sizes' => '152x152',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/images/icons/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/images/icons/icon-384x384.png',
                    'sizes' => '384x384',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ],
                [
                    'src' => '/images/icons/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ]
            ],
            'screenshots' => [
                [
                    'src' => '/images/screenshots/desktop-home.png',
                    'sizes' => '1280x720',
                    'type' => 'image/png',
                    'form_factor' => 'wide',
                    'label' => 'Homepage Desktop'
                ],
                [
                    'src' => '/images/screenshots/mobile-home.png',
                    'sizes' => '375x667',
                    'type' => 'image/png',
                    'form_factor' => 'narrow',
                    'label' => 'Homepage Mobile'
                ]
            ],
            'shortcuts' => [
                [
                    'name' => 'Pengajuan Surat',
                    'short_name' => 'Surat',
                    'description' => 'Ajukan surat keterangan secara online',
                    'url' => '/surat',
                    'icons' => [
                        [
                            'src' => '/images/icons/shortcut-surat.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ],
                [
                    'name' => 'Data Penduduk',
                    'short_name' => 'Penduduk',
                    'description' => 'Kelola data penduduk desa',
                    'url' => '/penduduk',
                    'icons' => [
                        [
                            'src' => '/images/icons/shortcut-penduduk.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ],
                [
                    'name' => 'Kartu Keluarga',
                    'short_name' => 'KK',
                    'description' => 'Kelola kartu keluarga',
                    'url' => '/kartu-keluarga',
                    'icons' => [
                        [
                            'src' => '/images/icons/shortcut-kk.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ],
                [
                    'name' => 'Statistik',
                    'short_name' => 'Stats',
                    'description' => 'Lihat statistik desa',
                    'url' => '/statistics',
                    'icons' => [
                        [
                            'src' => '/images/icons/shortcut-stats.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ]
            ],
            'related_applications' => [
                [
                    'platform' => 'web',
                    'url' => $appUrl
                ]
            ],
            'prefer_related_applications' => false,
            'edge_side_panel' => [
                'preferred_width' => 400
            ],
            'launch_handler' => [
                'client_mode' => 'navigate-existing'
            ]
        ];

        return response()->json($manifest)
            ->header('Content-Type', 'application/manifest+json')
            ->header('Cache-Control', 'public, max-age=3600'); // Cache 1 jam
    }
}
