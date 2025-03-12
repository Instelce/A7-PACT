<?php
/** @var $this \app\core\View */

/** @var $offersALaUne array */
/** @var $newOffers array */

use app\core\Application;

$this->title = "Home";
$this->cssFile = "home";
$this->jsFile = "home";
$this->threejs = true;
$this->noMain = true;
//$this->waves = true;

// $this->waves = true;

?>

<header>
    <div id="background"></div>

    <div class="header-content">
        <h1 class="title">
            Découvrez
            <div class="title-slide-container">
                <span class="title-slides">
                    <span style="--i: 0">Lannion</span>
                    <span style="--i: 1">Saint Brieuc</span>
                    <span style="--i: 2">Plouha</span>
                    <span style="--i: 3">Bréhat</span>
                    <span style="--i: 4">Trébeurden</span>
                </span>
            </div>
        </h1>

        <x-input rounded class="lg:w-[600px] sm:w-full">
            <input slot="input" type="text"
                   placeholder="Recherchez des activités, visites, spectacles..."
                   id="searchBar">
            <button slot="button" class="button only-icon sm" id="searchButton">
                <i data-lucide="search" stroke-width="2"></i>
            </button>
        </x-input>

    </div>
</header>

<section>
    <div class="home-display">
        <h1 class="home-category-title">Destinations phares</h1>

        <!-- Carousel -->
        <div class="carousel-gen" data-slides-visible="3" data-slides-to-scroll="1">

            <?php foreach ($offersALaUne as $offer) { ?>
                    <div class="home-card">
                        <a href="/offres/<?php echo $offer["id"]; ?>">
                        <!-- Image -->
                        <div class="image-container">
                            <img src="<?php echo $offer["image"]; ?>"
                                 alt="Image de <?php echo $offer['title']; ?>">
                            <!-- Nice background on hover -->
                            <img class="image-bg" src="<?php echo $offer["image"]; ?>"
                                 alt="Image de <?php echo $offer['title']; ?>">

                            <!-- Localization -->
                            <div class="flex gap-2 justify-center items-center localization">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                     viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="3"
                                     stroke-linecap="round"
                                     stroke-linejoin="round" class="lucide lucide-map-pin">
                                    <path
                                        d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <p><?php echo $offer["location"]; ?></p>
                            </div>

                            <!-- Stars + Avis -->
                            <div class="flex gap-2 stars-container">
                                <div class="stars" data-number="<?php echo $offer["rating"] ?>">
                                </div>
                                <!--<div class="flex gap-1">
                                        <p><?php /*echo $offer["ratingsCount"] */?></p>
                                        <p>Avis</p>
                                    </div>-->
                            </div>
                        </div>

                        <div class="card-content">
                            <!-- Title and summary -->
                            <h2><?php echo $offer["title"]; ?></h2>
                            <p class="summary text-ellipsis"><?php echo $offer["summary"]; ?></p>

                            <div class="flex flex-col gap-2">
                                <!-- Type + Professional -->
                                <p class="text-sm"><?php echo $offer["type"]; ?> proposé par <a
                                        href="/"><?php echo $offer["author"]; ?></a></p>



                                <!--<div class="price">
                                    <p class="price-text"><?php /*echo $offer["price"]; */?></p>
                                </div>-->
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</section>


<section class="home-display mt-12 mb-12 recently-consulted">
    <h2 class="home-category-title">Consulté récements</h2>

    <!-- Carousel -->
    <div class="recently-consulted-carousel">
        <!-- Generated in JS -->
    </div>
</section>


<section>
    <div class="home-display mt-12 mb-12">
        <h2 class="home-category-title">Nouvelles offres</h2>

        <!-- Carousel -->
        <div class="carousel-gen" data-slides-visible="3" data-slides-to-scroll="1">

            <?php foreach ($newOffers as $offer) { ?>
                <div class="home-card">
                    <a href="/offres/<?php echo $offer['id']; ?>">
                        <!-- Image -->
                        <div class="image-container">
                            <img src="<?php echo $offer["image"]; ?>"
                                 alt="Image de <?php echo $offer['title']; ?>">
                            <!-- Nice background on hover -->
                            <img class="image-bg" src="<?php echo $offer["image"]; ?>"
                                 alt="Image de <?php echo $offer['title']; ?>">

                            <!-- Localization -->
                            <div class="flex gap-2 justify-center items-center localization">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                     viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="3"
                                     stroke-linecap="round"
                                     stroke-linejoin="round" class="lucide lucide-map-pin">
                                    <path
                                        d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <p><?php echo $offer["location"]; ?></p>
                            </div>

                            <!-- Stars + Avis -->
                            <div class="flex gap-2 stars-container">
                                <div class="stars" data-number="<?php echo $offer["rating"] ?>">
                                </div>
                                <!--<div class="flex gap-1">
                                        <p><?php /*echo $offer["ratingsCount"] */?></p>
                                        <p>Avis</p>
                                    </div>-->
                            </div>
                        </div>

                        <div class="card-content">
                            <!-- Title and summary -->
                            <h2><?php echo $offer["title"]; ?></h2>
                            <p class="summary text-ellipsis"><?php echo $offer["summary"]; ?></p>

                            <div class="flex flex-col gap-2">
                                <!-- Type + Professional -->
                                <p class="text-sm"><?php echo $offer["type"]; ?> proposé par <a
                                        href="/"><?php echo $offer["author"]; ?></a></p>

                                <!--<div class="price">
                                    <p class="price-text"><?php /*echo $offer["price"]; */?></p>
                                </div>-->
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</section>


<section class="discover">
    <div class="circle-center">
<!--        <svg width="427" height="305" viewBox="0 0 427 305" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--            <path d="M112.141 4L100.635 12.877L71.7686 16.5085L65.3089 25.3856L45.1225 10.2543L18.6783 28.0083L28.7715 41.7274L11.2093 65.7357L10.4019 65.3322L3.13477 99.2263L18.6783 100.235L17.669 113.752L29.1752 121.015L18.6783 131.506L11.4112 136.752L12.4205 149.059L28.1659 154.304L13.4298 158.541V174.076L22.9174 186.584L24.9361 223.101L18.6783 229.356L23.9268 248.118L43.7095 253.364L45.7281 263.653L58.2437 264.662L68.7406 257.399L74.9984 263.653L99.0202 274.144L118.803 263.653L123.85 253.364L140.604 252.153L159.378 268.899L177.142 264.662L192.685 280.398H199.952L207.219 289.679H221.955L227.002 282.416L233.26 295.933L249.005 302.188L268.788 289.679V276.162L283.322 270.916H292.608L304.114 291.898L329.145 293.916L341.661 278.179L355.186 249.127L372.95 242.873L382.236 229.356L391.723 238.636L411.506 234.601L417.764 177.304L424.021 154.304L417.764 141.796L407.267 137.761L400.201 99.6298L392.127 108.709L368.105 106.086L365.683 120.007L350.543 121.217L349.332 103.665L336.614 99.8316L327.732 109.919V84.7003L312.592 95.9983L289.984 92.165L282.313 107.296L235.682 132.515V145.225H225.589V122.428L199.145 109.919L201.567 87.1213L177.747 69.569V48.1834L159.983 44.3501L161.194 24.1751L147.468 22.9646L148.679 9.04377H123.446L119.61 21.5523L112.141 4Z" stroke="#00A2FF" stroke-width="4"/>-->
<!--        </svg>-->

        <h1 class="home-category-title">Venez découvrir</h1>
    </div>

    <div class="cities">
        <!-- Generated in JS -->
    </div>

    <svg class="discover-background" width="421" height="299" viewBox="0 0 421 299" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M111.402 5.62952C110.19 2.78118 106.614 1.84626 104.163 3.73711L99.5018 7.33293C98.1914 8.34391 96.6344 8.98597 94.9923 9.19255L71.8906 12.0988C69.8125 12.3603 67.9348 13.4691 66.7024 15.1627V15.1627C64.212 18.5851 59.4029 19.3083 56.0161 16.7696L50.2982 12.4836C45.321 8.75282 38.5292 8.57628 33.3649 12.0435L23.8483 18.4327C19.3207 21.4724 18.2396 27.6729 21.4713 32.0656V32.0656C23.9505 35.4354 23.9568 40.024 21.4868 43.4005L8.28539 61.4475C8.16364 61.6139 7.9396 61.6683 7.75514 61.5761V61.5761C7.50796 61.4526 7.21066 61.5955 7.15273 61.8657L1.74455 87.0897C0.825235 91.3774 3.92815 95.4813 8.30409 95.7652V95.7652C12.2909 96.024 15.3008 99.4855 15.0033 103.47L14.8629 105.351C14.6606 108.059 15.9698 110.659 18.2667 112.108V112.108C22.25 114.623 22.87 120.184 19.5382 123.514L16.175 126.875C15.7547 127.295 15.3015 127.681 14.8196 128.029L12.8892 129.422C10.0262 131.489 8.45282 134.903 8.74144 138.422L9.0551 142.246C9.19499 143.952 10.3392 145.41 11.9629 145.951L13.2385 146.376C16.8321 147.573 16.7255 152.692 13.0852 153.739V153.739C11.4331 154.214 10.2951 155.725 10.2951 157.444V165.279C10.2951 168.391 11.313 171.418 13.1937 173.897L16.0857 177.71C18.4952 180.887 19.9003 184.712 20.1203 188.693L21.5897 215.274C21.7244 217.709 20.8153 220.087 19.0901 221.811V221.811C16.8993 224 16.0599 227.201 16.8943 230.184L18.1974 234.843C19.805 240.59 24.3335 245.057 30.1017 246.587L35.9911 248.148C38.7952 248.892 40.9291 251.17 41.4876 254.017V254.017C42.1415 257.35 44.9325 259.842 48.3182 260.115L51.2958 260.355C53.7617 260.553 56.2204 259.893 58.2547 258.485L59.6736 257.503C63.1093 255.126 67.7532 255.545 70.7082 258.498V258.498C71.4717 259.261 72.3714 259.875 73.3606 260.307L87.039 266.281C92.6281 268.722 99.0256 268.479 104.414 265.621L112.014 261.591C114.389 260.331 116.306 258.354 117.489 255.94V255.94C119.487 251.868 123.496 249.163 128.02 248.836L130.363 248.667C134.906 248.339 139.388 249.864 142.787 252.896L149.685 259.05C153.794 262.714 159.435 264.137 164.79 262.86V262.86C170.512 261.495 176.532 263.218 180.665 267.403L187.192 274.011C188.701 275.539 190.759 276.398 192.906 276.398V276.398C195.375 276.398 197.707 277.534 199.229 279.478L200.451 281.039C202.745 283.968 206.258 285.679 209.978 285.679H216.782C218.059 285.679 219.255 285.053 219.984 284.004V284.004C221.704 281.53 225.46 281.857 226.726 284.591L228.961 289.418C229.708 291.033 231.047 292.299 232.701 292.956L237.042 294.681C242.589 296.884 248.855 296.3 253.9 293.111L260.631 288.855C263.758 286.878 265.653 283.436 265.653 279.737V279.737C265.653 275.191 268.503 271.133 272.779 269.59L278.195 267.635C279.513 267.159 280.904 266.916 282.305 266.916V266.916C286.725 266.916 290.794 269.325 292.92 273.201L295.796 278.446C299.031 284.346 305.018 288.224 311.725 288.764L315.386 289.059C322.027 289.595 328.498 286.788 332.646 281.573L337.049 276.036C338.03 274.803 338.862 273.457 339.528 272.028L348.499 252.756C350.771 247.877 354.913 244.12 359.989 242.332L366.487 240.045C368.65 239.283 370.515 237.854 371.813 235.964L373.904 232.92C376.617 228.971 382.236 228.422 385.662 231.773V231.773C387.492 233.564 390.091 234.33 392.6 233.818L396.916 232.937C403.741 231.545 408.884 225.903 409.64 218.98L414.458 174.866C414.572 173.826 414.767 172.797 415.042 171.787L419.065 157C420.238 152.688 419.781 148.095 417.782 144.099L416.688 141.912C415.363 139.264 413.097 137.207 410.333 136.144V136.144C406.48 134.663 403.674 131.287 402.922 127.229L398.834 105.168C398.115 101.285 393.244 99.9276 390.62 102.879V102.879C389.599 104.027 388.085 104.61 386.558 104.443L372.663 102.926C368.338 102.453 364.39 105.423 363.644 109.71V109.71C362.999 113.416 359.927 116.216 356.177 116.516L355.978 116.532C351.26 116.909 347.142 113.362 346.816 108.641L346.671 106.533C346.386 102.399 343.573 98.8738 339.606 97.6781L335.99 96.5884C334.461 96.1273 332.802 96.6006 331.747 97.7997V97.7997C329.259 100.625 324.597 98.8656 324.597 95.1009V91.5309C324.597 87.0611 319.5 84.5045 315.917 87.1778L312.134 90.0013C310.425 91.2766 308.268 91.7966 306.166 91.4401L298.592 90.156C291.608 88.9719 284.666 92.4703 281.464 98.7881V98.7881C279.975 101.724 277.627 104.135 274.732 105.701L236.723 126.257C234.15 127.648 232.547 130.337 232.547 133.262V136.179C232.547 138.966 230.288 141.225 227.501 141.225V141.225C224.714 141.225 222.454 138.966 222.454 136.179V127.267C222.454 121.869 219.343 116.956 214.464 114.648L207.473 111.341C200.608 108.094 196.548 100.861 197.35 93.3091V93.3091C198.02 86.9994 195.293 80.8078 190.185 77.0437L181.24 70.4529C177.073 67.3818 174.612 62.513 174.612 57.336V52.9929C174.612 47.8528 171.026 43.4094 166.001 42.3252V42.3252C160.736 41.1889 157.087 36.3806 157.41 31.0035L157.652 26.9605C157.879 23.1827 155.058 19.9104 151.288 19.5779L150.986 19.5513C147.31 19.2271 144.592 15.9867 144.912 12.3105V12.3105C145.252 8.40332 142.172 5.04377 138.25 5.04377H123.091C121.439 5.04377 119.98 6.12211 119.496 7.70158V7.70158C118.468 11.0548 113.815 11.2988 112.441 8.07157L111.402 5.62952Z" fill="url(#paint0_radial_3489_10327)"/>
        <defs>
            <radialGradient id="paint0_radial_3489_10327" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(178.5 261) rotate(-71.2647) scale(241.285 340.57)">
                <stop offset="0.685" stop-color="#00A2FF" stop-opacity="0"/>
                <stop offset="1" stop-color="#00A2FF" stop-opacity="0.2"/>
            </radialGradient>
        </defs>
    </svg>
</section>
