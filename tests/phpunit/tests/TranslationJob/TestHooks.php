<?php

namespace WPML\PB\BeaverBuilder\TranslationJob;

/**
 * @group wpmlcore-6929
 */
class TestHooks extends \OTGS_TestCase {

	/**
	 * @test
	 */
	public function itShouldLoadOnFrontAndBackendWithDic() {
		$subject = $this->getSubject();
		$this->assertInstanceOf( \IWPML_Backend_Action::class, $subject );
		$this->assertInstanceOf( \IWPML_Frontend_Action::class, $subject );
		$this->assertInstanceOf( \IWPML_DIC_Action::class, $subject );
	}

	/**
	 * @test
	 */
	public function itShouldAddHooks() {
		$subject = $this->getSubject();
		\WP_Mock::expectFilterAdded( 'wpml_tm_translation_job_data', [ $subject, 'filterFieldsByPageBuilderKind' ], PHP_INT_MAX, 2 );
		$subject->add_hooks();
	}

	/**
	 * @test
	 */
	public function itShouldNotFilterFieldsIfNotAPostPackage() {
		$translationPackage = [
			'type'     => 'external',
			'contents' => [
				'title'                  => [ 'some data' ],
				'package-string-123-456' => [ 'some data' ],
			],
		];

		$post = (object) [ 'ID' => 999 ];

		$dataSettings = $this->getDataSettings();
		$dataSettings->expects( $this->never() )->method( 'is_handling_post' );

		$subject = $this->getSubject( $dataSettings );

		$this->assertSame(
			$translationPackage,
			$subject->filterFieldsByPageBuilderKind( $translationPackage, $post )
		);
	}

	/**
	 * @test
	 */
	public function itShouldNotFilterFieldsIfPostHasNoId() {
		$translationPackage = [
			'type'     => 'post',
			'contents' => [
				'title'                  => [ 'some data' ],
				'package-string-123-456' => [ 'some data' ],
			],
		];

		$post = (object) [ 'foo' => 'bar' ];

		$dataSettings = $this->getDataSettings();
		$dataSettings->expects( $this->never() )->method( 'is_handling_post' );

		$subject = $this->getSubject( $dataSettings );

		$this->assertSame(
			$translationPackage,
			$subject->filterFieldsByPageBuilderKind( $translationPackage, $post )
		);
	}

	/**
	 * @test
	 */
	public function itShouldFilterFieldsToRemoveGutenbergOnes() {
		$beaverPackageId    = 123;
		$gutenbergPackageId = 456;

		$stringPackages = [
			$beaverPackageId    => (object) [
				'kind_slug' => 'beaver-builder',
				'ID'        => $beaverPackageId,
			],
			$gutenbergPackageId => (object) [
				'kind_slug' => 'gutenberg',
				'ID'        => $gutenbergPackageId,
			],
		];

		$translationPackage = [
			'type'     => 'post',
			'contents' => [
				'title'                                   => [ 'some data' ],
				"package-string-$beaverPackageId-5200"    => [ 'some data' ],
				"package-string-$gutenbergPackageId-5201" => [ 'some data' ],
			],
		];

		$expectedTranslationPackage = [
			'type'     => 'post',
			'contents' => [
				'title'                                   => [ 'some data' ],
				"package-string-$beaverPackageId-5200"    => [ 'some data' ],
			],
		];

		$post = (object) [ 'ID' => 987 ];

		\WP_Mock::onFilter( 'wpml_st_get_post_string_packages' )
			->with( [], $post->ID )
			->reply( $stringPackages );

		$dataSettings = $this->getDataSettings();
		$dataSettings->method( 'is_handling_post' )->with( $post->ID )->willReturn( true );

		$subject = $this->getSubject( $dataSettings );

		$this->assertEquals(
			$expectedTranslationPackage,
			$subject->filterFieldsByPageBuilderKind( $translationPackage, $post )
		);
	}

	/**
	 * @test
	 */
	public function itShouldFilterFieldsToRemoveBeaverBuilderOnes() {
		$beaverPackageId    = 123;
		$gutenbergPackageId = 456;

		$stringPackages = [
			$beaverPackageId    => (object) [
				'kind_slug' => 'beaver-builder',
				'ID'        => $beaverPackageId,
			],
			$gutenbergPackageId => (object) [
				'kind_slug' => 'gutenberg',
				'ID'        => $gutenbergPackageId,
			],
		];

		$translationPackage = [
			'type'     => 'post',
			'contents' => [
				'title'                                   => [ 'some data' ],
				"package-string-$beaverPackageId-5200"    => [ 'some data' ],
				"package-string-$gutenbergPackageId-5201" => [ 'some data' ],
			],
		];

		$expectedTranslationPackage = [
			'type'     => 'post',
			'contents' => [
				'title'                                   => [ 'some data' ],
				"package-string-$gutenbergPackageId-5201" => [ 'some data' ],
			],
		];

		$post = (object) [ 'ID' => 987 ];

		\WP_Mock::onFilter( 'wpml_st_get_post_string_packages' )
			->with( [], $post->ID )
			->reply( $stringPackages );

		$dataSettings = $this->getDataSettings();
		$dataSettings->method( 'is_handling_post' )->with( $post->ID )->willReturn( false );

		$subject = $this->getSubject( $dataSettings );

		$this->assertEquals(
			$expectedTranslationPackage,
			$subject->filterFieldsByPageBuilderKind( $translationPackage, $post )
		);
	}

	private function getSubject( $dataSettings = null ) {
		$dataSettings = $dataSettings ?: $this->getDataSettings();
		return new Hooks( $dataSettings );
	}

	private function getDataSettings() {
		return $this->getMockBuilder( \WPML_Beaver_Builder_Data_Settings::class )
			->setMethods( [ 'is_handling_post' ] )
			->disableOriginalConstructor()->getMock();
	}
}
