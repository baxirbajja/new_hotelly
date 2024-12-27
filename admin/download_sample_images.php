<?php
// Create uploads directory if it doesn't exist
$uploads_dir = __DIR__ . '/../uploads';
if (!file_exists($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

// Sample image URLs (Moroccan-themed hotels and rooms)
$images = [
    'riad_marrakech.jpg' => 'https://images.unsplash.com/photo-1577493340887-b7bfff550145',
    'kasbah_atlas.jpg' => 'https://images.unsplash.com/photo-1539037116277-4db20889f2d4',
    'palais_fes.jpg' => 'https://images.unsplash.com/photo-1548588627-f978862b85e1',
    'marina_tanger.jpg' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b',
    'suite_royale.jpg' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461',
    'chambre_deluxe.jpg' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427',
    'suite_atlas.jpg' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843',
    'chambre_berbere.jpg' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28f8e',
    'suite_imperiale.jpg' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd',
    'chambre_medina.jpg' => 'https://images.unsplash.com/photo-1574643156929-51fa098b0394',
    'suite_mediterranee.jpg' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461',
    'chambre_ocean.jpg' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427'
];

// Download images
foreach ($images as $filename => $url) {
    $target_file = $uploads_dir . '/' . $filename;
    if (!file_exists($target_file)) {
        $image_data = file_get_contents($url);
        if ($image_data !== false) {
            file_put_contents($target_file, $image_data);
            echo "Downloaded $filename<br>";
        } else {
            echo "Failed to download $filename<br>";
        }
    } else {
        echo "$filename already exists<br>";
    }
}

echo "Image download process completed!";
?>
