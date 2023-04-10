<?php
App::uses('ZodiacController', 'Controller');
App::uses('ZodiacPerson', 'Lib/Zodiac/src');
App::uses('NatalWheel', 'Lib/Zodiac/src/charts');
App::uses('NatalAspects', 'Lib/Zodiac/src/charts');

class ZodiacNatalController extends ZodiacController {

    public function show () {
        $clientData = $this->retrieveClientFromDB( $this->param );

        $this->site_vars['meta_title'] = 'Résultat du thème natal';
        $this->site_vars['meta_description'] = '';

        $horoscopeOfPerson = $this->getHoroscopeOfPerson( $clientData[ 'ZodiacClient' ] );

        $chart = new NatalWheel( array_merge(
            $this->getChartConfigs(),
            [   // Not sure if this is needed. The user info can be displayed as an html.
                'header_text'      => $this->compileUserHeaderString( $clientData[ 'ZodiacClient' ] ),
            ]
        ));

        $aspectsTable = new NatalAspects( $this->getAspectsConfigs() );
		
		var_dump(strtolower( NatalWheel::SIGNS[ $horoscopeOfPerson->getHouses( 1 )->signIndex() ] ) );exit;

        $this->set([
            'natalChart'   => $this->imageResourceToVar( $chart->getImage( $horoscopeOfPerson ) ),
            'aspectsTable' => $this->imageResourceToVar( $aspectsTable->getImage( $horoscopeOfPerson ) ),
            'client'       => $clientData,
            'signs'        => NatalAspects::SIGNS,
            'horoscope'    => $horoscopeOfPerson,
            'houseSytems'  => $this->houseSystems,
            'hsys'         => $this->hsys,
            'interpret'    => $this->getAscendantInterpret( strtolower( NatalWheel::SIGNS[ $horoscopeOfPerson->getHouses( 1 )->signIndex() ] ) )
        ]);
    }

    private function getAscendantInterpret( $sign ) {
        $this->loadModel('ZodiacInterpret');

        return $this->ZodiacInterpret->find( 'first', [
            'fields'     => [ 'title', 'content' ],
            'conditions' => [
                'lang_id'  => $this->Session->read( 'Config.id_lang' ),
                'entity'   => 'ac',
                'position' => $sign
            ],
            'recursive'  => - 1
        ] )[ 'ZodiacInterpret' ];
    }

    /**
     * The config sources must be in the order of classes inheritance
     * so that children configs override those of parent class.
     * Some individual configs can be added here as well (for example, per user settings).
     *
     * @return array merged configs for the chart
     */
    private function getChartConfigs() {
        return array_merge(
            (array) Configure::read( 'Zodiac.paint' ),
            (array) Configure::read( 'Zodiac.cosmogramma' ),
            (array) Configure::read( 'Zodiac.natal_wheel' )
        );
    }

    private function getAspectsConfigs() {
        return array_merge(
            (array) Configure::read( 'Zodiac.paint' ),
            (array) Configure::read( 'Zodiac.aspects' ),
            (array) Configure::read( 'Zodiac.natal_aspects' )
        );
    }
}
