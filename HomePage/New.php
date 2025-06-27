<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Project/forms/loginform.html");
    exit();
}
$conn = new mysqli("localhost", "root", "", "hotel_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Kolkata'); // Set your timezone
$current_time = date('H:i');
$lunch_start = '12:00';
$lunch_end = '15:30';
$lunch_time_over = ($current_time < $lunch_start || $current_time > $lunch_end);

// Set shop open and close times
$shop_open = '07:00';
$shop_close = '23:00';
$shop_open_now = ($current_time >= $shop_open && $current_time <= $shop_close);

$userName = '';
if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT fullname FROM users WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($userName);
    $stmt->fetch();
    $stmt->close();
}

$sql_update_images = "
UPDATE menu_items SET image_url = CASE name
    WHEN 'Idli Sambar' THEN 'https://media.istockphoto.com/id/2159618247/photo/idli-vada-with-sambar.jpg?s=612x612&w=0&k=20&c=0HNP26WxESqfA3i3Xr1uTxxpKKYc69d9NRn9Dai4xok='
    WHEN 'Pongal' THEN 'https://www.spiceindiaonline.com/wp-content/uploads/2014/01/Ven-Pongal-3.jpg'
    WHEN 'Vada' THEN 'https://vaya.in/recipes/wp-content/uploads/2018/02/dreamstime_xs_44383666.jpg'
    WHEN 'Poori Masala' THEN 'https://palakkadbusiness.com/Gangashankaram/wp-content/uploads/sites/79/2023/11/Poori-Masala.png'
    WHEN 'Ghee Roast Dosa' THEN 'https://www.squarecut.net/wp-content/uploads/2024/08/crispy-crepes-made-barnyard-millets-lentils-commonly-known-as-milled-ghee-roast-dosa-plated-conical-shape-rolls-served-238893976.webp'
    WHEN 'Masala Dosa' THEN 'https://vismaifood.com/storage/app/uploads/public/8b4/19e/427/thumb__700_0_0_0_auto.jpg'
    WHEN 'Onion Dosa' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/06/onion-dosa-recipe.jpg'
    WHEN 'Rava Dosa' THEN 'https://www.vegrecipesofindia.com/wp-content/uploads/2021/08/rava-dosa-recipe-1.jpg'
    WHEN 'Plain Dosa' THEN 'https://static.toiimg.com/thumb/63841366.cms'
    WHEN 'Uthappam' THEN 'https://www.vegrecipesofindia.com/wp-content/uploads/2016/07/onion-uttapam-recipe.jpg'
    WHEN 'Upma' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/05/upma-recipe.jpg'
    WHEN 'Medu Vada' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/07/medu-vada-recipe.jpg'
    WHEN 'Mysore Bonda' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2014/09/mysore-bonda-recipe.jpg'
    WHEN 'Chutney' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2014/05/coconut-chutney-recipe.jpg'
    WHEN 'Sambar' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/05/sambar-recipe.jpg'
    WHEN 'Paneer Butter Masala' THEN 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgIhLcOIgSfPph9kwyJScX0oZOf9W6XT26Chnlc5uXPP4C8_52cTsozMURL_SDruHd-DQtC9GLHqWKFvqHvnWlsqULIkpwga-6KTUiXW1btD7KQI7oNmljdwykZ1WGZB7QZr8fsqGgqoy4/s2048/paneer+butter+masala+15.JPG'
    WHEN 'Vegetable Biryani' THEN 'https://media.istockphoto.com/id/179085494/photo/indian-biryani.jpg?s=612x612&w=0&k=20&c=VJAUfiuavFYB7PXwisvUhLqWFJ20-9m087-czUJp9Fs='
    WHEN 'Dal Tadka' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSY1l_jZRmr6YriO6mvEXEofhE1yhpb5HES1w&s'
    WHEN 'Kadai Mushroom' THEN 'https://static.toiimg.com/photo/62997250.cms'
    WHEN 'Chicken 65' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQuBWfoWkhdZVPd16GGTM93qjTG7AWwPULmPA&s'
    WHEN 'Mutton Curry' THEN 'https://atanurrannagharrecipe.com/wp-content/uploads/2023/03/Best-Mutton-Curry-Recipe-Atanur-Rannaghar.jpg'
    WHEN 'Fish Curry' THEN 'https://www.recipetineats.com/tachyon/2020/10/Goan-Fish-Curry_6-SQ.jpg'
    WHEN 'Prawn Masala' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_0Z0p1dWKj_Ltu3e_kEqMHAGy7HalMdX8oQ&s'
    WHEN 'Sambar Rice' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1VeM9HJJksvX4STFIUUyFUmMwRe3F_ddKag&s'
    WHEN 'Curd Rice' THEN 'https://maharajaroyaldining.com/wp-content/uploads/2024/03/Curd-Rice-1.webp'
    WHEN 'Lemon Rice' THEN 'https://www.flavourstreat.com/wp-content/uploads/2020/12/turmeric-lemon-rice-recipe-02.jpg'
    WHEN 'Coconut Rice' THEN 'https://static.toiimg.com/thumb/52413325.cms?imgsize=190896&width=800&height=800'
    WHEN 'Parotta' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQl2ExoO9ArN3mJ13eP-4AoLHhmgYrDGXCL4Q&s'
    WHEN 'Naan' THEN 'https://www.thespruceeats.com/thmb/MReCj8olqrCsPaGvikesPJie02U=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/naan-leavened-indian-flatbread-1957348-final-08-116a2e523f6e4ee693b1a9655784d9b9.jpg'
    WHEN 'Chapati' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_freDZrX7LsLnPPwG27dGa443MeYjcsE_mQ&s'
    WHEN 'Butter Roti' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTGFVv0p_3-hNCCIR_1gVoJoXv7YHTCbYzQGw&s'
    WHEN 'Gulab Jamun' THEN 'https://carveyourcraving.com/wp-content/uploads/2020/09/gulab-jamun-mousse-layered-dessert.jpg'
    WHEN 'Payasam' THEN 'https://www.whiskaffair.com/wp-content/uploads/2020/11/Semiya-Payasam-2-3.jpg'
    WHEN 'Masala Chai' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR8LMM6j0uSjpwGASdoFVtMLW_iojIyFp6ZfQ&s'
    WHEN 'Rasmalai' THEN 'https://prashantcorner.com/cdn/shop/files/RasmalaiSR-2.png?v=1720595089&width=1946'
    WHEN 'Jalebi' THEN 'https://static.toiimg.com/thumb/53099699.cms?imgsize=182393&width=800&height=800'
    WHEN 'Filter Coffee' THEN 'https://www.clubmahindra.com/blog/media/section_images/indianfilt-351110d18aec48f.jpg'
    WHEN 'Buttermilk' THEN 'https://static.toiimg.com/thumb/msid-76625491,imgsize-957295,width-400,resizemode-4/76625491.jpg'
    WHEN 'Fresh Lime Soda' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQIxacpaTgsCyexsCPzWztI8aIFGqnZ3bAKzA&s'
    WHEN 'Onion Pakoda' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTIT8uIqE1VMbvmPrCEQr_Pm7_t9JT486YuxQ&s'
    WHEN 'Paneer 65' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQX-aS8DhH5p_6IFqic0Y4WAfLnbvOjRVkaGA&s'
    WHEN 'Gobi Manchurian' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJSTFk6lnhBZh05OqwHyuyjjzhrL6321XVUw&s'
    WHEN 'Papad' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQgIjv2Y9pyRYsvpuwQI4DTQ3Qc4YfqGpzXYQ&s'
    WHEN 'Bisi Bele Bath' THEN 'https://www.vegrecipesofindia.com/wp-content/uploads/2018/12/bisi-bele-bath-recipe-1.jpg'
    WHEN 'Veg Thali' THEN 'https://www.vegrecipesofindia.com/wp-content/uploads/2021/08/south-indian-thali-1.jpg'
    WHEN 'Mushroom Biryani' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2022/07/mushroom-biryani-recipe.jpg'
    WHEN 'Paneer Tikka Masala' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/08/paneer-tikka-masala-recipe.jpg'
    WHEN 'Chicken Biryani' THEN 'https://www.licious.in/blog/wp-content/uploads/2020/12/Hyderabadi-chicken-Biryani.jpg'
    WHEN 'Fish Fry Meals' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/07/fish-fry-recipe.jpg'
    WHEN 'Mutton Biryani' THEN 'https://www.licious.in/blog/wp-content/uploads/2020/12/Mutton-Biryani.jpg'
    WHEN 'Tomato Rice' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/07/tomato-rice-recipe.jpg'
    WHEN 'Jeera Rice' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/01/jeera-rice-recipe.jpg'
    WHEN 'Pulao' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/07/veg-pulao-recipe.jpg'
    WHEN 'Mixed Veg Curry' THEN 'https://www.vegrecipesofindia.com/wp-content/uploads/2013/05/mixed-veg-curry-recipe-1.jpg'
    WHEN 'Malai Kofta' THEN 'https://www.vegrecipesofindia.com/wp-content/uploads/2021/04/malai-kofta-1.jpg'
    WHEN 'Paneer Makhani' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2022/06/paneer-makhani-recipe.jpg'
    WHEN 'Veg Kolhapuri' THEN 'https://www.vegrecipesofindia.com/wp-content/uploads/2013/06/veg-kolhapuri-recipe.jpg'
    WHEN 'Chicken Chettinad' THEN 'https://www.licious.in/blog/wp-content/uploads/2020/12/Chicken-Chettinad.jpg'
    WHEN 'Fish Curry' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/07/fish-curry-recipe.jpg'
    WHEN 'Mutton Pepper Fry' THEN 'https://www.archanaskitchen.com/images/archanaskitchen/Indian_Non_Veg_Recipes/Mutton_Pepper_Fry_Recipe.jpg'
    WHEN 'Prawn Masala' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/07/prawn-masala-recipe.jpg'
    WHEN 'Garlic Naan' THEN 'https://www.cookwithmanali.com/wp-content/uploads/2020/05/Garlic-Naan.jpg'
    WHEN 'Butter Naan' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2019/05/butter-naan-recipe.jpg'
    WHEN 'Ghee Rice' THEN 'https://www.indianhealthyrecipes.com/wp-content/uploads/2021/07/ghee-rice-recipe.jpg'
    WHEN 'Kashmiri Pulao' THEN 'https://www.vegrecipesofindia.com/wp-content/uploads/2021/12/kashmiri-pulao-recipe-1.jpg'
    WHEN 'Omelette' THEN 'https://www.allrecipes.com/thmb/xb0_9ETJEeeld-xZTfOHGvR446s=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/262697ham-and-cheese-omelettefabeveryday4x3-831275518e14417a9c1f695ce59e24d3.jpg'
    WHEN 'Pancakes' THEN 'https://www.savoryexperiments.com/wp-content/uploads/2022/01/Funfetti-Pancakes-5.jpg'
    WHEN 'French Toast' THEN 'https://somebodyfeedseb.com/wp-content/uploads/2023/02/2022.06.11-Savory-French-Toast-1128.jpg'
    WHEN 'Breakfast Burrito' THEN 'https://www.makeaheadmealmom.com/wp-content/uploads/2023/08/BreakfastBurritos_Featured_compressed.jpg'
    WHEN 'Granola Parfait' THEN 'https://newsite.susanjoyfultable.com/site/assets/files/1339/chia_and_granola_parfait-1.jpg'
    WHEN 'Avocado Toast' THEN 'https://californiaavocado.com/wp-content/uploads/2020/07/California-Avocado-Toast-Three-Ways.jpeg'
    WHEN 'Meen Kuzhambu' THEN 'https://www.foodiaq.com/wp-content/uploads/2024/05/meen-kulambu-1.jpg'
    WHEN 'Grilled Chicken Sandwich' THEN 'https://www.chicken.ca/wp-content/uploads/2020/09/Canadian-BBQ.jpg'
    WHEN 'Steak with Mashed Potatoes' THEN 'https://i.pinimg.com/736x/34/07/f5/3407f5e6e9714a0ef2d3646cdd903467.jpg'
    WHEN 'Spaghetti Bolognese' THEN 'https://www.kitchensanctuary.com/wp-content/uploads/2019/09/Spaghetti-Bolognese-square-FS-0204.jpg'
    WHEN 'Club Sandwich' THEN 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/club_sandwich_16496_16x9.jpg'
    WHEN 'Veggie Wrap' THEN 'https://s.lightorangebean.com/media/20240914152454/Fresh-Veggie-Hummus-Wrap_-done-830x521.png'
    WHEN 'Chicken Quesadilla' THEN 'https://www.foodnetwork.com/content/dam/images/food/fullset/2013/2/5/1/WU0404H_chicken-quesadillas-recipe_s4x3.jpg'
    WHEN 'Cheeseburger' THEN 'https://www.awesomecuisine.com/wp-content/uploads/2014/01/Double-Cheeseburger.jpg'
    WHEN 'chicken quesadilla' THEN 'https://zenaskitchen.com/wp-content/uploads/2022/08/chipotle-bbq-chicken-quesadillas.jpg'
    WHEN 'Cheeseburger' THEN 'https://www.sargento.com/assets/Uploads/Recipe/Image/burger_0__FillWzgwMCw4MDBd.jpg'
    WHEN 'Beef Tacos' THEN 'https://oliviaadriance.com/wp-content/uploads/2023/07/Final_3_Crispy_Baked_Beef_Tacos_grain-free-dairy-free.jpg.webp'
    WHEN 'Lamb Curry' THEN 'https://www.ocado.com/cmscontent/recipe_image_large/34731104.jpg?bQAP'
    WHEN 'Caesar Salad' THEN 'https://assets.farmison.com/images/recipe-detail-1380/74550-classic-chicken-caesar-salad.jpg'
    WHEN 'Tomato Soup' THEN 'https://mahatmarice.com/wp-content/uploads/2019/08/Chicken-Tomato-Basil-Rice-Soup.jpg'
    WHEN 'Egg Masala Curry' THEN 'https://eggs.ca/wp-content/uploads/2024/06/Kerala-Coconut-Egg-Curry-1664x834-1.jpg'
    WHEN 'Masala Dosai' THEN 'https://vismaifood.com/storage/app/uploads/public/fc8/6e9/476/thumb__700_0_0_0_auto.jpg'
    WHEN 'Idli with Sambar & Chutney' THEN 'https://storypick.com/wp-content/uploads/2018/03/idli-sambar.jpg'
    WHEN 'Kuzhi Paniyaram' THEN 'https://images.news18.com/webstories/uploads/2024/10/20220210_1952372-2024-10-ae64828ee36d05b56496a715affe9e59.jpg'
    WHEN 'Chapati with Vegetable Kurma' THEN 'https://sangskitchen.b-cdn.net/wp-content/uploads/2018/08/Veg-kurma-thumbnail.jpg'
    WHEN 'Nethili Meen Fry' THEN 'https://desertfoodfeed.com/wp-content/uploads/2020/08/nethili-fry2-3-800x620.jpg'
    WHEN 'Parotta with Chicken Salna' THEN 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEij5P6MuGj1iptIJdll-jyp7lOd0ahnOWRQgBmBezEkWuefffr_nQrXW9wsJlYW1OHSRPinH7SLMJp3qiJHje4k13fiFku_qiFR4as0k0eQVm_mdgWny1NLNxlz6a_LUIToc88q0xtq1Bg/w1200-h630-p-k-no-nu/DSC_0280-2.jpg'
    WHEN 'Kothu Parotta (Chicken)' THEN 'https://i.pinimg.com/736x/70/62/6a/70626ace5b289ec60cae9999bc80fcef.jpg'
    WHEN 'Veg Kothu Parotta' THEN 'https://images.herzindagi.info/image/2024/Apr/Kothu-Parotta.jpg'
    WHEN 'Vegetable Stir Fry' THEN 'https://playswellwithbutter.com/wp-content/uploads/2025/02/Vegetable-Stir-Fried-Noodles-17.jpg'
    WHEN 'Chocolate Cake' THEN 'https://cdn.sprinklebakes.com/media/2023/08/Death-By-Chocolate-Cake-2.jpg'
    WHEN 'Ice Cream Sundae' THEN 'https://ticktocktea.com/cdn/shop/articles/Ice-Cream-Sundae-800x800px-min.jpg?v=1657010846'
    WHEN 'Cheesecake' THEN 'https://butternutbakeryblog.com/wp-content/uploads/2020/04/cheesecake-slice.jpg'
    WHEN 'Fruit Salad' THEN 'https://www.foxyfolksy.com/wp-content/uploads/2018/12/filipino-fruit-salad-640-500x500.jpg'
    WHEN 'Brownie' THEN 'https://icecreamfromscratch.com/wp-content/uploads/2022/07/Brownie-Sundae-1.2.jpg'
    ELSE image_url
END
WHERE name IN (
    'Idli Sambar', 'Pongal', 'Vada', 'Poori Masala', 'Ghee Roast Dosa','Granola Parfait','Beef Tacos',
    'Masala Dosa', 'Onion Dosa', 'Rava Dosa', 'Plain Dosa', 'Uthappam','Avocado Toast','Lamb Curry',
    'Upma', 'Medu Vada', 'Mysore Bonda', 'Chutney', 'Sambar','Grilled Chicken Sandwich','Caesar Salad',
    'Paneer Butter Masala', 'Vegetable Biryani', 'Dal Tadka', 'Kadai Mushroom','Steak with Mashed Potatoes',
    'Chicken 65', 'Mutton Curry', 'Fish Curry', 'Prawn Masala','Spaghetti Bolognese','Tomato Soup',
    'Sambar Rice', 'Curd Rice', 'Lemon Rice', 'Coconut Rice','Club Sandwich','Cheeseburger','Egg Masala Curry',
    'Parotta', 'Naan', 'Chapati', 'Butter Roti','French Toast','Veggie Wrap','chicken quesadilla',
    'Gulab Jamun', 'Payasam', 'Rasmalai', 'Jalebi','Masala Dosai','Idli with Sambar & Chutney','Brownie'
    'Masala Chai', 'Filter Coffee', 'Buttermilk', 'Fresh Lime Soda','Kuzhi Paniyaram','Ice Cream Sundae',
    'Onion Pakoda', 'Paneer 65', 'Gobi Manchurian', 'Papad','Chapati with Vegetable Kurma','Cheesecake',
    'Bisi Bele Bath', 'Veg Thali', 'Mushroom Biryani', 'Paneer Tikka Masala','Chocolate Cake',
    'Chicken Biryani', 'Fish Fry Meals', 'Mutton Biryani','Nethili Meen Fry','Vegetable Stir Fry',
    'Tomato Rice', 'Jeera Rice', 'Pulao','Chicken Quesadilla''Parotta with Chicken Salna','Fruit Salad',
    'Mixed Veg Curry', 'Malai Kofta', 'Paneer Makhani', 'Veg Kolhapuri','Kothu Parotta (Chicken)',
    'Chicken Chettinad', 'Fish Curry', 'Mutton Pepper Fry', 'Prawn Masala','Veg Kothu Parotta',
    'Garlic Naan', 'Butter Naan', 'Ghee Rice', 'Kashmiri Pulao','Omelette','Pancakes','Breakfast Burrito'
);
";
if ($conn->query($sql_update_images) === FALSE) {
    die("Error updating images: " . $conn->error);
}
$sql = "SELECT * FROM menu_items ORDER BY meal_time, category";
$result = $conn->query($sql);
$menu_items_by_time = [
    'breakfast' => [],
    'lunch' => [],
    'dinner' => [],
    'Desserts' => []
];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $meal_time = $row['meal_time'] ?? 'all';
        $category = $row['category'];
        
        // Skip Desserts, Breads, and Beverages for all meal times
        // if ($category == 'Desserts' || $category == 'Breads' || $category == 'Beverages') {
            
            
        // }
        
        // Categorize remaining items
        if ($meal_time == 'breakfast')
        {
            $menu_items_by_time['breakfast'][$category][] = $row;
        }
        else if ($meal_time == 'lunch')
        {
            $menu_items_by_time['lunch'][$category][] = $row;
        }
        else if ($meal_time == 'dinner')
        {
            $menu_items_by_time['dinner'][$category][] = $row;
        }
        else if ($meal_time == 'all')
        {           
            // $menu_items_by_time['lunch'][$category][] = $row;
            // $menu_items_by_time['dinner'][$category][] = $row;
            $menu_items_by_time['Desserts'][] = $row;
        } else {
            $menu_items_by_time[$meal_time][$category][] = $row;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chellappa Hotel - Authentic Tamil Cuisine</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css\home.css">
</head>
<body>
    <header class="header">
        <h1 class="logo">CHELLAPPA HOTEL</h1>
        <p class="subtitle">Authentic Tamil Cuisine Since 1985</p>
        <div id="clockDisplay" class="clock"></div>
    </header>
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="nav-item active" data-section="home"><i class="fas fa-home"></i> Home</a>
            <a href="#" class="nav-item" data-section="offers"><i class="fas fa-percent"></i> Offers</a>
            <a href="#" class="nav-item" data-section="location"><i class="fas fa-map-marker-alt"></i> Location</a>
            <a href="#" class="nav-item" data-section="about"><i class="fas fa-info-circle"></i> About</a>
            <a href="#" class="nav-item" data-section="contact"><i class="fas fa-phone-alt"></i> Contact</a>
            <a href="../HomePage/profile/profile.php" class="profile-link">
    <i class="fas fa-user-circle"></i> My Profile
</a>



            <div class="language-selector">
                <select id="languageSelect" onchange="changeLanguage(this.value)">
                    <option value="en">English</option>
                    <option value="ta">தமிழ்</option>
                    <option value="hi">हिंदी</option>
                </select>
            </div>
        </div>
    </nav>
    <div class="container" id="home">
        <h2 class="section-title">Our Signature Dishes</h2>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search menu items...">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="menu-grid">
            <div class="meal-time-tabs">
                <button class="meal-tab" data-meal="breakfast">Breakfast</button>
                <button class="meal-tab" data-meal="lunch">Lunch</button>
                <button class="meal-tab" data-meal="dinner">Dinner</button>
                <button class="meal-tab" data-meal="all">Show All</button>
                <button class="meal-tab" data-meal="desserts">Desserts</button>
            </div>
            <div class="meal-section" id="breakfast" style="display: block;">
                <h2 class="meal-title">Breakfast Menu</h2>
                <?php foreach ($menu_items_by_time['breakfast'] as $category => $items): ?>
                    <div class="category-section">
                        <h3 class="category-title"><?php echo htmlspecialchars($category); ?></h3>
                        <div class="category-items">
                            <?php foreach ($items as $item): ?>
                                <div class="menu-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                                    <div class="menu-item-img-container">
                                        <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/150'); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="menu-item-img"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/150';">
                                        <div class="availability-badge" 
                                             data-stock="<?php echo htmlspecialchars($item['stock_quantity']); ?>" 
                                             data-available="<?php echo $item['stock_quantity'] > 0 ? 'true' : 'false'; ?>">
                                            <?php if($item['stock_quantity'] > 0): ?>
                                                <?php if($item['stock_quantity'] <= 5): ?>
                                                    <span class="in-stock low">Only <?php echo $item['stock_quantity']; ?> left</span>
                                                <?php else: ?>
                                                    <span class="in-stock">In Stock (<?php echo $item['stock_quantity']; ?>)</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="out-of-stock">Out of Stock</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="menu-item-content">
                                        <h3 class="menu-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <p class="menu-item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                                        <div class="menu-item-footer">
                                            <span class="menu-item-price"><?php echo htmlspecialchars($item['price']); ?></span>
                                            <button class="add-to-cart" <?php echo $item['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="meal-section" id="lunch" style="display: none;">
                <h2 class="meal-title">Lunch Menu</h2>
                <?php foreach ($menu_items_by_time['lunch'] as $category => $items): ?>
                    <div class="category-section">
                        <h3 class="category-title"><?php echo htmlspecialchars($category); ?></h3>
                        <div class="category-items">
                            <?php foreach ($items as $item): ?>
                                <div class="menu-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                                    <div class="menu-item-img-container">
                                        <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/150'); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="menu-item-img"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/150';">
                                        <div class="availability-badge" 
                                             data-stock="<?php echo htmlspecialchars($item['stock_quantity']); ?>" 
                                             data-available="<?php echo $item['stock_quantity'] > 0 ? 'true' : 'false'; ?>">
                                            <?php if ($lunch_time_over): ?>
                                                <span class="lunch-over">Lunch time is over</span>
                                                <span class="end"></span>
                                            <?php else: ?>
                                                <?php if($item['stock_quantity'] > 0): ?>
                                                    <?php if($item['stock_quantity'] <= 5): ?>
                                                        <span class="in-stock low">Only <?php echo $item['stock_quantity']; ?> left</span>
                                                    <?php else: ?>
                                                        <span class="in-stock">In Stock (<?php echo $item['stock_quantity']; ?>)</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="out-of-stock">Out of Stock</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="menu-item-content">
                                        <h3 class="menu-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <p class="menu-item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                                        <div class="menu-item-footer">
                                            <span class="menu-item-price"><?php echo htmlspecialchars($item['price']); ?></span>
                                            <button class="add-to-cart" <?php echo ($item['stock_quantity'] <= 0 || $lunch_time_over) ? 'disabled' : ''; ?>>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="meal-section" id="dinner" style="display: none;">
                <h2 class="meal-title">Dinner Menu</h2>
                <?php foreach ($menu_items_by_time['dinner'] as $category => $items): ?>
                    <?php if ($category == 'Desserts') continue; // Skip Desserts in Dinner ?>
                    <div class="category-section">
                        <h3 class="category-title"><?php echo htmlspecialchars($category); ?></h3>
                        <div class="category-items">
                            <?php foreach ($items as $item): ?>
                                <div class="menu-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                                    <div class="menu-item-img-container">
                                        <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/150'); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="menu-item-img"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/150';">
                                        <div class="availability-badge" 
                                             data-stock="<?php echo htmlspecialchars($item['stock_quantity']); ?>" 
                                             data-available="<?php echo $item['stock_quantity'] > 0 ? 'true' : 'false'; ?>">
                                            <?php if($item['stock_quantity'] > 0): ?>
                                                <?php if($item['stock_quantity'] <= 5): ?>
                                                    <span class="in-stock low">Only <?php echo $item['stock_quantity']; ?> left</span>
                                                <?php else: ?>
                                                    <span class="in-stock">In Stock (<?php echo $item['stock_quantity']; ?>)</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="out-of-stock">Out of Stock</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="menu-item-content">
                                        <h3 class="menu-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <p class="menu-item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                                        <div class="menu-item-footer">
                                            <span class="menu-item-price"><?php echo htmlspecialchars($item['price']); ?></span>
                                            <button class="add-to-cart" <?php echo $item['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="meal-section" id="desserts" style="display: none;">
    <h2 class="meal-title">Desserts Menu</h2>
    <div class="category-section" data-meal="desserts">
        <h3 class="category-title">Desserts</h3>
        <div class="category-items">
            <?php foreach ($menu_items_by_time['Desserts'] as $item): ?>
                <div class="menu-item" data-category="Desserts">
                    <div class="menu-item-img-container">
                        <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/150'); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             class="menu-item-img"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/150';">
                        <div class="availability-badge" data-stock="<?php echo htmlspecialchars($item['stock_quantity']); ?>" data-available="<?php echo ($shop_open_now && $item['stock_quantity'] > 0) ? 'true' : 'false'; ?>">
                            <?php if (!$shop_open_now): ?>
                                <span class="out-of-stock">Shop Closed</span>
                            <?php elseif($item['stock_quantity'] <= 0): ?>
                                <span class="out-of-stock">Out of Stock</span>
                            <?php else: ?>
                                <span class="in-stock">Available</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="menu-item-content">
                        <h3 class="menu-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="menu-item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="menu-item-footer">
                            <span class="menu-item-price"><?php echo htmlspecialchars($item['price']); ?></span>
                            <button class="add-to-cart" <?php echo (!$shop_open_now || $item['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

        </div>
        <div class="monthly-offers" id="monthlyOffers" style="display: none;">
            <h3 class="offers-title">This Month's Special Offers</h3>
            <ul class="offer-list">
                <li class="offer-item">
                    <div class="offer-day">Monday - Thursday</div>
                    <div class="offer-desc">15% off on all main courses during lunch hours (12PM - 3PM)</div>
                </li>
                <li class="offer-item">
                    <div class="offer-day">Friday</div>
                    <div class="offer-desc">Buy 1 Get 1 Free on all desserts from 6PM onwards</div>
                </li>
                <li class="offer-item">
                    <div class="offer-day">Saturday</div>
                    <div class="offer-desc">Family special: 20% off on total bill for groups of 4 or more</div>
                </li>
                <li class="offer-item">
                    <div class="offer-day">Sunday</div>
                    <div class="offer-desc">Grand buffet lunch with unlimited dishes for just ₹350 per person</div>
                </li>
                <li class="offer-item">
                    <div class="offer-day">Everyday</div>
                    <div class="offer-desc">10% discount for senior citizens and students (valid ID required)</div>
                </li>
            </ul>
        </div>
        <div class="about-section" id="about" style="display: none;">
            <div class="about-content">
                <div class="about-text">
                    <h3>Our Heritage</h3>
                    <p>Established in 1985, Chellappa Hotel has been serving authentic Tamil cuisine for over three decades. What began as a small eatery in Tenkasi has grown into a beloved culinary landmark, cherished by locals and visitors alike.</p>
                    <p>Our founder, Mr. Chellappa, started with a simple vision: to preserve the traditional flavors of Tamil Nadu while maintaining uncompromising quality. Today, his legacy continues under the guidance of his son, who upholds the same values of authenticity and hospitality.</p>
                    <p>We take pride in using only the freshest local ingredients, traditional cooking methods, and recipes passed down through generations. Each dish tells a story of our rich culinary heritage.</p>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Chellappa Hotel Interior">
                </div>
            </div>
        </div>
        <div class="contact-section" id="contact" style="display: none;">
            <div class="contact-content">
                <div class="contact-info">
                    <h3>Get In Touch</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 12 Temple Road, Near Thirumalai Nayakkar Mahal, Tenkasi, Tamil Nadu 627811</p>
                    <p><i class="fas fa-phone-alt"></i> +91 4634 223344</p>
                    <p><i class="fas fa-mobile-alt"></i> +91 98765 43210</p>
                    <p><i class="fas fa-envelope"></i> info@chellappahotel.com</p>
                    <p><i class="fas fa-clock"></i> Open Daily: 7:00 AM - 11:00 PM</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="contact-form">
                    <h3>Send Us a Message</h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" required>
                            <div class="error-message">Please enter your name</div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                            <div class="error-message">Please enter a valid email</div>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" required></textarea>
                            <div class="error-message">Please enter your message</div>
                        </div>
                        <button type="submit" class="submit-btn" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                        <div class="success-message" id="successMessage">
                            <i class="fas fa-check-circle"></i>
                            <p>Thank you for your message! We'll contact you soon.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="cart-preview" id="cartPreview">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cartCount">0</span>
    </div>
    <div class="bill-modal" id="billModal">
        <div class="bill-container" id="billContainer">
            <div class="bill-header">
                <h3 class="bill-title">Your Order Bill</h3>
                <button class="close-bill" id="closeBill">&times;</button>
            </div>
            <div class="bill-content">
                <div class="bill-items" id="billItems">
                    <div class="empty-cart">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Your cart is empty</p>
                    </div>
                </div>
                <?php if (isset($_SESSION['user_email'])): ?>
                    <div class="order-email">Order placed by: <?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                <?php endif; ?>
                <div class="dining-options">
                    <h4>Select Dining Option</h4>
                    <div class="option-container">
                        <div class="option-item">
                            <input type="radio" id="roomDelivery" name="diningOption" value="room">
                            <label for="roomDelivery">Room Delivery</label>
                            <input type="text" id="roomNumber" class="option-input" placeholder="Enter Room Number" disabled>
                        </div>
                        <div class="option-item">
                            <input type="radio" id="tableService" name="diningOption" value="table">
                            <label for="tableService">Table Service</label>
                            <input type="text" id="tableNumber" class="option-input" placeholder="Enter Table Number" disabled>
                        </div>
                        <div class="option-item">
                            <input type="radio" id="takeaway" name="diningOption" value="takeaway">
                            <label for="takeaway">Takeaway</label>
                            <div id="takeawayDetails" class="option-details" style="display: none;">
                                <input type="text" id="customerName" class="option-input" placeholder="Your Name" disabled>
                                <input type="tel" id="phoneNumber" class="option-input" placeholder="Phone Number" disabled>
                                <input type="time" id="arrivalTime" class="option-input" placeholder="Pickup Time" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bill-total">
                    <span>Total:</span>
                    <span class="bill-total-amount" id="billTotal">₹0</span>
                </div>
                <div id="waitingId" class="waiting-id" style="display: none;">
                    <div class="waiting-info">
                        <i class="fas fa-receipt"></i>
                        <h3>Your Waiting ID</h3>
                        <div class="id-number"></div>
                        <p class="waiting-message">Please keep this ID for your reference</p>
                    </div>
                </div>
                <div class="bill-actions">
                    <button class="bill-btn bill-btn-secondary" id="clearCart">
                        <i class="fas fa-trash-alt"></i> Clear Cart
                    </button>
                    <button class="bill-btn bill-btn-primary" id="checkoutBtn">
                        <i class="fas fa-credit-card"></i> Proceed to Pay
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="location-modal" id="locationModal">
        <div class="location-container" id="locationContainer">
            <div class="location-header">
                <h3 class="location-title">Our Location</h3>
                <button class="close-location" id="closeLocation">&times;</button>
            </div>
            <div class="location-content">
                <div class="location-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3932.041389346015!2d77.30818931537399!3d8.95598019364245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b0429c8b5c3a4a5%3A0x7e9b9ef9f0e7f4d5!2sChellappa%20Hotel%2C%20Tenkasi!5e0!3m2!1sen!2sin!4v1620000000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <div class="location-details">
                    <div class="location-info">
                        <h4>Chellappa Hotel</h4>
                        <p><i class="fas fa-map-marker-alt"></i> 12 Temple Road, Near Thirumalai Nayakkar Mahal, Tenkasi, Tamil Nadu 627811</p>
                        <p><i class="fas fa-clock"></i> Open Daily: 7:00 AM - 11:00 PM</p>
                        <p><i class="fas fa-phone-alt"></i> +91 4634 223344</p>
                        <p><i class="fas fa-envelope"></i> info@chellappahotel.com</p>
                        <div class="action-buttons">
                            <a href="https://www.google.com/maps/dir//Chellappa+Hotel,+12+Temple+Road,+Near+Thirumalai+Nayakkar+Mahal,+Tenkasi,+Tamil+Nadu+627811/@8.9559801,77.3081893,17z/data=!4m8!4m7!1m0!1m5!1m1!1s0x3b0429c8b5c3a4a5:0x7e9b9ef9f0e7f4d5!2m2!1d77.310378!2d8.9559801" target="_blank" class="direction-btn">
                                <i class="fas fa-directions"></i> Get Directions
                            </a>
                            <a href="tel:+914634223344" class="call-btn">
                                <i class="fas fa-phone"></i> Call Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-column">
                <h3>Chellappa Hotel</h3>
                <p>Experience authentic Tamil cuisine in the heart of Tenkasi. Our restaurant blends traditional flavors with warm hospitality for an unforgettable dining experience.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="#" data-section="home">Home</a></li>
                    <li><a href="#" data-section="offers">Offers</a></li>
                    <li><a href="#" data-section="location">Location</a></li>
                    <li><a href="#" data-section="about">About Us</a></li>
                    <li><a href="#" data-section="contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Opening Hours</h3>
                <div class="contact-info">
                    <p><i class="fas fa-clock"></i> Monday - Sunday: 7:00 AM - 11:00 PM</p>
                    <p><i class="fas fa-utensils"></i> Breakfast: 7:00 AM - 11:00 AM</p>
                    <p><i class="fas fa-lunch"></i> Lunch: 12:00 PM - 3:30 PM</p>
                    <p><i class="fas fa-moon"></i> Dinner: 6:30 PM - 11:00 PM</p>
                </div>
            </div>
            <div class="footer-column">
                <h3>Contact Info</h3>
                <div class="contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> 12 Temple Road, Near Thirumalai Nayakkar Mahal, Tenkasi, Tamil Nadu 627811</p>
                    <p><i class="fas fa-phone-alt"></i> +91 4634 223344</p>
                    <p><i class="fas fa-envelope"></i> info@chellappahotel.com</p>
                </div>
            </div>
        </div>
        <div class="copyright">
            &copy; 2023 Chellappa Hotel. All Rights Reserved. | GSTIN: 33AAAAA0000A1Z5
        </div>
    </footer>
    <script src="scriptfile/clock.js"></script>
</body>
<script src="translations.js"></script>
<script src="script.js"></script>
</html>
