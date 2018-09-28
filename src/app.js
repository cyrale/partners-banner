/**
 * Internal dependencies
 */
import './sass/app.scss';

const $ = window.jQuery;

$( () => {
	const initCarousel = ( $banner ) => {
		const options = $banner.data( 'options' );

		$banner.find( '.partners-banner__carousel' ).slick( {
			arrows: false,
			dots: true,
			infinite: true,
			autoplay: true,
			autoplaySpeed: options.carousel_autoplay_speed,
			speed: options.carousel_speed,
			slidesToShow: options.carousel_slides_to_show,
			slidesToScroll: options.carousel_slides_to_show,
			responsive: [
				{
					breakpoint: 640,
					settings: {
						slidesToShow: options.carousel_slides_to_show_mobile,
						slidesToScroll: options.carousel_slides_to_show_mobile,
					},
				},
			],
		} );
	};

	const initRandom = ( $banner ) => {
		const options = $banner.data( 'options' );
		const $container = $banner.find( '.partners-banner__random' );
		const $partners = $banner.find( '.partner' );
		const displayed = [];

		$partners.remove();

		options.random_layout.map( itemPerRow => {
			const $row = $( '<div />' ).addClass( 'partners-banner__row' );

			( [ ...Array( itemPerRow ).keys() ] ).map( () => {
				const index = randomize( $partners.length, displayed );

				displayed.push( index );
				$row.append( $partners[ index ] );
			} );

			$container.append( $row );
		} );

		setInterval( () => {
			const index = randomize( $partners.length, displayed );
			const position = Math.floor( Math.random() * displayed.length );
			const $target = $container.find( '.partner' ).eq( position );

			$target
				.append( $partners.eq( index ).html() )
				.addClass( 'partner--hide' );

			setTimeout( () => {
				displayed.splice( position, 1 );
				displayed.push( index );

				$target.find( '.partner__item' ).first().remove();

				$target
					.addClass( 'partner--show' );

				setTimeout( () => {
					$target
						.removeClass( 'partner--hide' )
						.removeClass( 'partner--show' );
				}, options.random_speed );
			}, options.random_speed );
		}, options.random_autoplay_speed + ( options.random_speed * 2 ) );
	};

	const randomize = ( max, already ) => {
		let rand;

		while ( already.indexOf( rand = Math.floor( Math.random() * max ) ) >= 0 ) {}

		return rand;
	};

	$( '.partners-banner' ).each( ( index, banner ) => {
		const $banner = $( banner );

		if ( $banner.data( 'layout' ) === 'carousel' ) {
			initCarousel( $banner );
		} else if ( $banner.data( 'layout' ) === 'random' ) {
			initRandom( $banner );
		}
	} );
} );

