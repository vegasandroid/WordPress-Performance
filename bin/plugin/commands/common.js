/**
 * External dependencies
 */
const path = require( 'path' );
const glob = require( 'fast-glob' );
const fs = require( 'fs' );
const { log, formats } = require( '../lib/logger' );

/**
 * @typedef WPModuleData
 *
 * @property {string}  slug         Module slug.
 * @property {string}  focus        Module focus.
 * @property {string}  name         Module name.
 * @property {string}  description  Module description.
 * @property {boolean} experimental Whether the module is experimental.
 */

/**
 * Definition of the groups areas as an object with a number to specify the order priority of each,
 * with increments of 10 between each module, in order to allow space for any change down the road.
 */
const FOCUS_AREAS = {
	images: 10,
	'js-and-css': 20,
	database: 30,
	measurement: 40,
	'object-cache': 50,
};

/**
 * Returns a promise resolving to the list of data for all modules.
 *
 * @param {string} modulesDir Modules directory.
 *
 * @return {Promise<[]WPModuleData>} Promise resolving to module data list.
 */
exports.getModuleData = async ( modulesDir ) => {
	const moduleFilePattern = path.join( modulesDir, '*/*/load.php' );
	const moduleFiles = await glob( path.resolve( '.', moduleFilePattern ) );

	return moduleFiles
		.map( ( moduleFile ) => {
			// Populate slug and focus based on file path.
			const moduleDir = path.dirname( moduleFile );
			const moduleData = {
				slug: path.basename( moduleDir ),
				focus: path.basename( path.dirname( moduleDir ) ),
			};

			// Map of module header => object property.
			const headers = {
				'Module Name': 'name',
				Description: 'description',
				Experimental: 'experimental',
			};

			// Populate name, description and experimental based on module file headers.
			const fileContent = fs.readFileSync( moduleFile, 'utf8' );
			const regex = new RegExp(
				`^(?:[ \t]*<?php)?[ \t/*#@]*(${ Object.keys( headers ).join(
					'|'
				) }):(.*)$`,
				'gmi'
			);
			let match = regex.exec( fileContent );
			while ( match ) {
				const content = match[ 2 ].trim();
				const prop = headers[ match[ 1 ] ];
				if ( content && prop ) {
					moduleData[ prop ] = content;
				}
				match = regex.exec( fileContent );
			}

			// Parse experimental field into a boolean.
			if ( typeof moduleData.experimental === 'string' ) {
				moduleData.experimental =
					moduleData.experimental.toLowerCase() === 'yes';
			}

			return moduleData;
		} )
		.filter( ( moduleData ) => {
			const requiredProperties = [];
			if ( ! moduleData.name ) {
				requiredProperties.push( 'name' );
			}

			if ( ! moduleData.description ) {
				requiredProperties.push( 'description' );
			}

			if ( typeof moduleData.experimental === 'undefined' ) {
				requiredProperties.push( 'experimental' );
			}

			if ( requiredProperties.length >= 1 ) {
				const properties = requiredProperties
					.map( ( property ) => `'${ property }'` )
					.join( ', ' );
				log(
					formats.warning(
						`This module was not included because it misses required properties: ${ properties }.\nDetails: ${ JSON.stringify(
							moduleData,
							null,
							4
						) }`
					)
				);
			}

			return (
				moduleData.name &&
				moduleData.description &&
				typeof moduleData.experimental !== 'undefined'
			);
		} )
		.sort( ( firstModule, secondModule ) => {
			// Not the same focus group.
			if ( firstModule.focus !== secondModule.focus ) {
				return (
					FOCUS_AREAS[ firstModule.focus ] -
					FOCUS_AREAS[ secondModule.focus ]
				);
			}

			if ( firstModule.experimental !== secondModule.experimental ) {
				return firstModule.experimental ? 1 : -1;
			}

			// Lastly order alphabetically.
			return firstModule.slug.localeCompare( secondModule.slug );
		} );
};
