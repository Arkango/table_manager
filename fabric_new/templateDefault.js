// se l'evento non è gia settato creo i tavoli

		/*****************creo layout di default*************************/

	/*
	 * in questo layout ci sono tre file verticali
	 * le due laterali con 14 tavoli a sinistra e 12 a destra
	 * la fila centrale ha solo all'inizio il tavolo degli sposi ed alla fine vicino l'entrata il tavolo della musica
	 *
	 */


	//************************************** RIEMPIMENTO CANVAS ******************************************************************


	//********************************** FILE SPOSO *****************************************************************************
	while(fila != 3) {													//ciclo che crea le due file di tavoli al lato sposo
		for(i = 1; i <= maxTable; i++) { 								//crea gli elementi in base al massimo di elementi definiti
			createTable(startTop,startLeft,tableName,"Sposo"+tableName,fila);
			tableName += 1;
			startTop += definedMargin;									//incremento il margine dall'alto
		}//***********end for*************************
		fila ++; 														//cambio fila creo la seconda lato sposo
		if (fila == 2) {
			startTop =  canvasWidth*0.06; 								//quando è la seconda fila si ricomincia dall'alto
			startLeft = startLeft+padding+radius+padding; 				//spazio che occupa un tavolo
		}
	}//**************end while*************************

	//******************************** FILA CENTRALE ****************************************************************************

	startLeft = canvasWidth / 2; 				//punto di partenza da sinistra del tavolo centrale
	tableName += 1;														//incremento l'id del tavolo
	createTable(tavoloBase.top,startLeft,tableName,"Sposi",2); //tavolo degli sposi
	tableName += 1;														//incremento l'id del tavolo
	createTable(canvasHeight-padding-radius-padding,startLeft,tableName,"Operatori",2); //tavolo degli operatori

	/********************************* FILE DELLA SPOSA **************************************************/
    fila = 1;															//ricomincio la fila per far girare il while
	startTop =  canvasWidth*0.06;											//ricomincia dall'alto
	startLeft = canvasWidth*0.96; 											//punto di partenza rispetto al tavolo centrale

	while(fila != 3){													//crea le due file di tavoli lato sposa
		for(i = 1; i < maxTable; i++) { 								//crea tutti gli elementi definiti
			if (fila == 2){
				startLeft = tavolo 										//il margine sinistro è diminutito della dimensione di un tavolo
			}
			tableName += 1;												//incremento il mio id univoco
			createTable(startTop,startLeft,tableName,"Sposa"+tableName,2); //tavoli lato sposa
			startTop += definedMargin;									//incremento margine dall'alto
		}//end for
		fila ++;														//incremento la fila
		if (fila == 2) {
			startTop =  canvasWidth*0.06;								//ricomincia dall'alto
			tavolo= startLeft-padding-radius-padding; 					//spazio che occupa un tavolo
		}
	}//end while *********************************


	//************************************* FINE RIEMPIMENTO CANVAS *****************************************************
