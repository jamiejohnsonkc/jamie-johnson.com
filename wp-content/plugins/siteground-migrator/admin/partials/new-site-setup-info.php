<?php
$temp_url    = ! empty( $status['temp_url'] ) ? $status['temp_url'] : '';
$dns_servers = ! empty( $status['dns_servers'] ) ? $status['dns_servers'] : array();
?>
<div class="flex flex--gutter-medium flex--margin-medium new-site-info hidden">
	<div class="box box--direction-row box--sm-6 box--flex box--temp-url ua-margin-top-medium <?php echo empty( $temp_url ) ? 'hidden' : ''; ?>">
		<div class="border-box ua-flex-grow">
			<span class="icon icon--presized with-color border-box__icon" style="width: 40px; height: 40px;">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46 44">
				   <path fill="#c1aa95" d="M43,0H3A3,3,0,0,0,0,3V41a3,3,0,0,0,3,3H43a3,3,0,0,0,3-3V3A3,3,0,0,0,43,0Zm1,41a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V11H44ZM2,9V3A1,1,0,0,1,3,2H43a1,1,0,0,1,1,1V9Z"/>
				   <path fill="#c1aa95" d="M15,14H5V39H15ZM13,37H7V16h6Z"/>
				   <path fill="#c1aa95" d="M28,14H18v8H28Zm-2,6H20V16h6Z"/>
				   <path fill="#c1aa95" d="M41,14H31v8H41Zm-2,6H33V16h6Z"/>
				   <path fill="#c1aa95" d="M28,25H18V39H28ZM26,37H20V27h6Z"/>
				   <path fill="#c1aa95" d="M41,25H31V39H41ZM39,37H33V27h6Z"/>
				   <circle fill="#c1aa95" cx="5.5" cy="5.5" r="1.5"/>
				   <circle fill="#c1aa95" cx="10.5" cy="5.5" r="1.5"/>
				   <circle fill="#c1aa95" cx="15.5" cy="5.5" r="1.5"/>
				</svg>
			</span>
			<div class="ua-margin-bottom-medium">
				<h3 class="title title--density-none title--level-4 typography typography--align-center typography--weight-bold with-color with-color--color-darker">
					<?php esc_html_e( 'Check Site', 'siteground-migrator' ); ?>
				</h3>

				<p class="text text--size-medium typography typography--weight-regular with-color with-color--color-dark">
					<?php esc_html_e( 'We’ve provided a temporary URL for you to check your site before pointing your nameservers to SiteGround. Мake sure everything is working fine before pointing your domain.', 'siteground-migrator' ); ?>
				</p>
			</div>
			<a href="<?php echo $temp_url; ?>" class="btn btn--temp-url btn--secondary btn--large btn--outlined ua-margin-top-auto" target="_blank"><span class="btn__content"><span class="btn__text"><?php esc_html_e( 'Go to Site', 'siteground-migrator' ) ?></span></span></a>
		</div>
	</div>

	<div class="box box--direction-row box--dns-servers box--sm-6 box--flex ua-margin-top-medium <?php echo empty( $dns_servers ) ? 'hidden' : ''; ?>">
		<div class="border-box ua-flex-grow">
			<span class="icon icon--presized with-color border-box__icon" style="width: 44px; height: 44px;">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
				   <rect x="28" y="18" width="2" height="2" fill="#c1aa95"/>
				   <rect x="18" y="18" width="2" height="2" fill="#c1aa95"/>
				   <rect x="28" y="28" width="2" height="2" fill="#c1aa95"/>
				   <rect x="18" y="28" width="2" height="2" fill="#c1aa95"/>
				   <path d="M47.405,14.506c-.022-.022-.032-.05-.056-.07s-.028-.013-.04-.022a23.941,23.941,0,0,0-32.612,0c-.013.01-.03.012-.043.023a.751.751,0,0,0-.061.076,24,24,0,1,0,32.812,0ZM52.975,31h-10a46.9,46.9,0,0,0-.924-8.584A3.989,3.989,0,0,0,44,19c0-.12-.025-.233-.035-.35a17.393,17.393,0,0,0,2.743-2.032A21.93,21.93,0,0,1,52.975,31ZM36,19a3.894,3.894,0,0,0,.053.522A26.218,26.218,0,0,1,32,19.974V10.106c2.156.457,4.148,2.414,5.74,5.6A4,4,0,0,0,36,19Zm4-2a2,2,0,1,1-2,2A2,2,0,0,1,40,17Zm0-2c-.12,0-.232.025-.349.035a16.816,16.816,0,0,0-2.843-4.251A21.961,21.961,0,0,1,45.24,15.25a15.24,15.24,0,0,1-1.956,1.476A3.993,3.993,0,0,0,40,15ZM30,10.11v9.857a24.462,24.462,0,0,1-7.017-1.227C24.616,13.924,27.121,10.716,30,10.11ZM21.131,18a17.324,17.324,0,0,1-4.367-2.756,21.958,21.958,0,0,1,8.463-4.473A19.768,19.768,0,0,0,21.131,18ZM30,21.967V31H23.858a3.994,3.994,0,0,0-2.7-2.81A41.514,41.514,0,0,1,22.4,20.67,26.639,26.639,0,0,0,30,21.967ZM20,30a2,2,0,1,1-2,2A2,2,0,0,1,20,30Zm3.858,3H30v5.142a3.984,3.984,0,0,0-2.96,3.465,24.186,24.186,0,0,0-4.788,1.176,42.2,42.2,0,0,1-1.1-6.973A3.994,3.994,0,0,0,23.858,33ZM31,44a2,2,0,1,1,2-2A2,2,0,0,1,31,44Zm-3.669-.415A4,4,0,0,0,30,45.858V53.89c-2.988-.628-5.573-4.057-7.2-9.179A22.166,22.166,0,0,1,27.331,43.585Zm-2.1,9.641a21.961,21.961,0,0,1-8.676-4.657,16.54,16.54,0,0,1,4.406-3.063A20.193,20.193,0,0,0,25.227,53.226ZM32,53.892V45.858a4,4,0,0,0,2.669-2.273A22,22,0,0,1,39.2,44.717C37.585,49.807,35.01,53.263,32,53.892Zm2.96-12.285A3.984,3.984,0,0,0,32,38.142V33h8.978a44.161,44.161,0,0,1-1.227,9.783A24.2,24.2,0,0,0,34.96,41.607ZM32,31V21.974a27.983,27.983,0,0,0,4.827-.565A3.985,3.985,0,0,0,40,23c.043,0,.083-.011.126-.013A45.005,45.005,0,0,1,40.975,31ZM15.289,16.62a18.776,18.776,0,0,0,5.238,3.327,42.923,42.923,0,0,0-1.368,8.145A4,4,0,0,0,16.142,31H9.025A21.931,21.931,0,0,1,15.289,16.62ZM9.025,33h7.117a4,4,0,0,0,3.017,2.908,43.741,43.741,0,0,0,1.224,7.65A18.518,18.518,0,0,0,15.1,47.186,21.923,21.923,0,0,1,9.025,33ZM41.037,45.511a16.792,16.792,0,0,1,4.41,3.06,21.975,21.975,0,0,1-8.667,4.653A20.426,20.426,0,0,0,41.037,45.511ZM46.9,47.185a18.488,18.488,0,0,0-5.283-3.63A45.17,45.17,0,0,0,42.988,33h9.987A21.914,21.914,0,0,1,46.9,47.185Z" transform="translate(-7 -8)" fill="#c1aa95"/>
				</svg>
			</span>
			<div class="ua-margin-bottom-medium">
				<h3 class="title title--density-none title--level-4 typography typography--align-center typography--weight-bold with-color with-color--color-darker"><?php esc_html_e( 'Update Your DNS', 'siteground-migrator' ); ?></h3>
				<p class="text text--size-medium typography typography--weight-regular with-color with-color--color-dark"><?php esc_html_e( 'Please change your domain’s NS. Note that those changes require up to 48 hours of propagation time. Don’t modify your site during that period to avoid data loss.', 'siteground-migrator' ); ?></p>
			</div>

			<div class="dns_servers">
				<?php
				foreach ( $dns_servers as $counter => $server ) :
					// Bail if the dns server is empty.
					if ( empty( $server ) ) {
						continue;
					}
				?>
					<h4 class="title title--density-compact title--level-5 typography typography--align-center typography--weight-light with-color with-color--color-darker">NS<?php echo $counter + 1; ?>: <a class="link"><?php echo esc_html( $server ); ?></a></h4>
				<?php endforeach ?>
			</div>
		</div>
	</div>

    <div class="box box--direction-row box--sm-12 box--flex box--temp-url ua-margin-top-medium">
        <div class="border-box ua-flex-grow">
            <div class="ua-margin-bottom-medium">
                <h3 class="title title--density-none title--level-4 typography typography--align-center typography--weight-bold with-color with-color--color-darker"><?php echo esc_html__( 'That went smoothly, right?', 'siteground-migrator' ) ?></h3>
                <p class="text text--size-medium typography typography--weight-regular with-color with-color--color-dark">
                    <a href="https://wordpress.org/support/plugin/siteground-migrator/reviews/#new-post" target="_blank" class="link"><?php echo esc_html__( 'Help us help other people by rating this plugin on WP.org!', 'siteground-migrator' ) ?></a>
                </p>
            </div>
            <a href="https://wordpress.org/support/plugin/siteground-migrator/reviews/#new-post" target="_blank" class="link">
                <span class="icon icon--presized with-color border-box__icon icon--rating">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 21">
                        <path fill="#25b8d2" d="M11,18l5.2,2.866a1.244,1.244,0,0,0,1.77-1.382L17,13l4.552-3.371a1.243,1.243,0,0,0-.494-2.161L15,6,12.079.626a1.243,1.243,0,0,0-2.158,0L7,6,.942,7.468A1.243,1.243,0,0,0,.448,9.629L5,13l-.969,6.484A1.244,1.244,0,0,0,5.8,20.866Z"></path>
                    </svg>
                </span>
                <span class="icon icon--presized with-color border-box__icon icon--rating">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 21">
                        <path fill="#25b8d2" d="M11,18l5.2,2.866a1.244,1.244,0,0,0,1.77-1.382L17,13l4.552-3.371a1.243,1.243,0,0,0-.494-2.161L15,6,12.079.626a1.243,1.243,0,0,0-2.158,0L7,6,.942,7.468A1.243,1.243,0,0,0,.448,9.629L5,13l-.969,6.484A1.244,1.244,0,0,0,5.8,20.866Z"></path>
                    </svg>
                </span>
                <span class="icon icon--presized with-color border-box__icon icon--rating">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 21">
                        <path fill="#25b8d2" d="M11,18l5.2,2.866a1.244,1.244,0,0,0,1.77-1.382L17,13l4.552-3.371a1.243,1.243,0,0,0-.494-2.161L15,6,12.079.626a1.243,1.243,0,0,0-2.158,0L7,6,.942,7.468A1.243,1.243,0,0,0,.448,9.629L5,13l-.969,6.484A1.244,1.244,0,0,0,5.8,20.866Z"></path>
                    </svg>
                </span>
                <span class="icon icon--presized with-color border-box__icon icon--rating">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 21">
                        <path fill="#25b8d2" d="M11,18l5.2,2.866a1.244,1.244,0,0,0,1.77-1.382L17,13l4.552-3.371a1.243,1.243,0,0,0-.494-2.161L15,6,12.079.626a1.243,1.243,0,0,0-2.158,0L7,6,.942,7.468A1.243,1.243,0,0,0,.448,9.629L5,13l-.969,6.484A1.244,1.244,0,0,0,5.8,20.866Z"></path>
                    </svg>
                </span>
                <span class="icon icon--presized with-color border-box__icon icon--rating">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 21">
                        <path fill="#25b8d2" d="M11,18l5.2,2.866a1.244,1.244,0,0,0,1.77-1.382L17,13l4.552-3.371a1.243,1.243,0,0,0-.494-2.161L15,6,12.079.626a1.243,1.243,0,0,0-2.158,0L7,6,.942,7.468A1.243,1.243,0,0,0,.448,9.629L5,13l-.969,6.484A1.244,1.244,0,0,0,5.8,20.866Z"></path>
                    </svg>
                </span>
            </a>
        </div>
    </div>

</div>