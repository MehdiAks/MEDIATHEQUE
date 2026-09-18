const SITE_VERSION = "2026-09-12-1";

const youtubeVideos = {
  G01: '<iframe width="560" height="315" src="https://www.youtube.com/embed/yVhS5Qsq2XM?si=usBttqc0bLSk7amd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G02: '<iframe width="560" height="315" src="https://www.youtube.com/embed/Q5rgOF-AsSA?si=fly2jI8QX3wZqepf" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G03: '<iframe width="560" height="315" src="https://www.youtube.com/embed/H-5_lNKP1Og?si=EAAA5M-oAvqW16Ja" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G04: '<iframe width="560" height="315" src="https://www.youtube.com/embed/5p7WofFvur4?si=5msPsYVizERLehIZ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G05: '<iframe width="560" height="315" src="https://www.youtube.com/embed/XlM0xJIFB80?si=Xt6aF0aeWFe_XINr" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G06: '<iframe width="560" height="315" src="https://www.youtube.com/embed/DYy-0JuNubI?si=BSwmc2ZxVqaHIDTE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G07: '<iframe width="560" height="315" src="https://www.youtube.com/embed/pLnwM-e9rp0?si=6veDbDPbz975qb_x" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G08: '<iframe width="560" height="315" src="https://www.youtube.com/embed/X7_BqAkjWF8?si=_a9uTFrxPY8KO6Tx" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G09: '<iframe width="560" height="315" src="https://www.youtube.com/embed/U43Luifi9UM?si=eqmXXbRJh5yGkaj2" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G10: '<iframe width="560" height="315" src="https://www.youtube.com/embed/Vu7qYi_ru58?si=3hRN2VhiKdkvfZ7u" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G11: '<iframe width="560" height="315" src="https://www.youtube.com/embed/1fu3v8ZZikc?si=5PAiB-8IyrsNqeCc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G12: '<iframe width="560" height="315" src="https://www.youtube.com/embed/ycDO-OvKC9c?si=W_niP1Zw35jN1ivg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G13: '<iframe width="560" height="315" src="https://www.youtube.com/embed/V_R-YPYhl2s?si=RW68kFvy8pUttL2K" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G14: '<iframe width="560" height="315" src="https://www.youtube.com/embed/eBy5AF8ewJM?si=p3hH_9BzUV5kb58y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G15: '<iframe width="560" height="315" src="https://www.youtube.com/embed/hZTntX5LKP0?si=of5LIZzg9EN02nL7" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G16: '<iframe width="560" height="315" src="https://www.youtube.com/embed/05bd7rWbQSQ?si=j6X3-_rlYkiuREwh" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G17: '<iframe width="560" height="315" src="https://www.youtube.com/embed/EzFizuPBF1Q?si=O-JBNqUIUBW6XtYn" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G18: '<iframe width="560" height="315" src="https://www.youtube.com/embed/ITeX1Q9Ex14?si=_ln8Goj3gOsv6XGG" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G19: '<iframe width="560" height="315" src="https://www.youtube.com/embed/f4h3haqYifA?si=pTDTxLkDIWGU1RUA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  G20: '<iframe width="560" height="315" src="https://www.youtube.com/embed/1l8tIDxaf0I?si=399VMUTWwwDX0Jn2" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
  backstage: '<iframe width="560" height="315" src="https://www.youtube.com/embed/5V5hi0cD7xs?si=mZLqWiQcCRAEf20p" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
};

const projectCredits = {
  G01: { title: "Gavé", formats: {
    "Vidéo": { "1A": ["Herzi Maïa", "Mangeret Tom", "Pinardaud Romain"], "2A": ["Grard Charline", "Rougerie Julien"], "3A": ["Amourdom Kyara", "Rossi Elia"] },
    "Affiche": { "1A": ["Araujo Emma", "Hallonet Ulysse", "Argans Esteban"], "2A": ["Achbli Hajar", "Ledda Enzo"], "3A": ["Fort Edouard", "Alletru Léane"] },
    "Site Web": { "1A": ["Fernandes Thomas", "Mirault Malakaï"], "2A": ["Kabamba Mulowayi Djuste", "Sever Sibel", "Theodore Lennon"], "3A": ["Detaevernier Tom", "Oudart Matthias"] }
  } },
  G02: { title: "Rive Droite vs Rive Gauche", formats: {
    "Vidéo": { "1A": ["Laborieux Eloïse", "Munier Gauthier"], "2A": ["Soulard Remi", "Theodore Lennon"], "3A": ["Alletru Léane", "Von Borowski Thibaud"] },
    "Affiche": { "1A": ["Harymbat Oïhan", "Vastra Ylan", "Benchabane-Vallot Louane"], "2A": ["Picard Manon", "Sever Sibel"], "3A": ["Lombart--Bertolus Val", "Rossi Elia"] },
    "Site Web": { "1A": ["Mongo Malcom", "Yapmis Umut"], "2A": ["Donato-Derouen Macsven", "Bezombes Romain"], "3A": ["Flourez Lili-Jeanne", "Amourdom Kyara"] }
  } },
  G03: { title: "Le Batcub", formats: {
    "Vidéo": { "1A": ["Galatoire--Saby Julian", "Maleplate Lysandre"], "2A": ["Donato-Derouen Macsven", "Kabamba Mulowayi Djuste"], "3A": ["Aazaoui Hind", "Moreau Mélannie"] },
    "Affiche": { "1A": ["Castex Manon", "Mahhad Ilyess"], "2A": ["Czerwinski Valentin", "Rougerie Julien"], "3A": ["Elbs Linda", "Tcha Jordan"] },
    "Site Web": { "1A": ["Plotkin Froger Tao", "Vastra Ylan"], "2A": ["Piot Maëva", "Rgana El Hami Yassmine"], "3A": ["Fort Edouard", "Pedder Stephen"] }
  } },
  G04: { title: "Le miroir d’eau sans eau", formats: {
    "Vidéo": { "1A": ["Chevallier Enzo", "Delatre Isaac", "Ecotière Paul"], "2A": ["Bezombes Romain", "Sever Sibel"], "3A": ["Brégué Manon", "Fort Edouard"] },
    "Affiche": { "1A": ["Gautelier Dorian", "Hauvel Clémence", "Pinardaud Romain"], "2A": ["Theodore Lennon", "Berger Louise"], "3A": ["Flourez Lili-Jeanne", "Pedder Stephen"] },
    "Site Web": { "1A": ["Galatoire--Saby Julian", "Hallonet Ulysse", "Baille Lucas"], "2A": ["Grard Charline", "Soulard Remi"], "3A": ["Elbs Linda", "Pasticier Lucas"] }
  } },
  G05: { title: "La gourde oubliée", formats: {
    "Vidéo": { "1A": ["Fernandes Thomas", "Vastra Ylan", "Verdier Tinéo"], "2A": ["Czerwinski Valentin", "Messean Théo"], "3A": ["Flourez Lili-Jeanne", "Tea Hélène"] },
    "Affiche": { "1A": ["Marzouk Salma", "Ayres-Beaume Allan", "Brieux Noah"], "2A": ["Bruno Lisa", "Kabamba Mulowayi Djuste"], "3A": ["Amourdom Kyara", "Brégué Manon"] },
    "Site Web": { "1A": ["Harymbat Oïhan", "Kontrec Louka", "Argans Esteban"], "2A": ["Rougerie Julien", "Sim Loana"], "3A": ["Moreau Mélannie", "Von Borowski Thibaud"] }
  } },
  G06: { title: "Le choix du resto à midi", formats: {
    "Vidéo": { "1A": ["Hallonet Ulysse", "Harymbat Oïhan", "Laud Lakisha"], "2A": ["Bruno Lisa", "Lotf Khalid"], "3A": ["Lombart--Bertolus Val", "Pedder Stephen"] },
    "Affiche": { "1A": ["Harmane Adam", "Octobon Madelyn"], "2A": ["Grard Charline", "Piot Maëva", "Bonaventure-Sanchez Alvin"], "3A": ["Detaevernier Tom", "Moreau Mélannie"] },
    "Site Web": { "1A": ["Finucci Romain", "Pinardaud Romain", "Balester Tom"], "2A": ["Czerwinski Valentin", "Gogan Cydji"], "3A": ["Lombart--Bertolus Val", "Alletru Léane"] }
  } },
  G07: { title: "Le café à 40 centimes", formats: {
    "Vidéo": { "1A": ["Argans Esteban", "Finucci Romain"], "2A": ["Ben Si Ali Maryam", "Piot Maëva", "Pauly Paul"], "3A": ["Nobilet Ywan", "Tcha Jordan"] },
    "Affiche": { "1A": ["Galatoire--Saby Julian", "Balester Tom"], "2A": ["Lacome Thaïs", "Attou Yanis", "Bezombes Romain"], "3A": ["Von Borowski Thibaud", "Astabie Margaux"] },
    "Site Web": { "1A": ["Hauvel Clémence", "Levy Jelly", "Bey Sofiane"], "2A": ["Martone Helio", "Williot Tom"], "3A": ["Rossi Elia", "Brégué Manon"] }
  } },
  G08: { title: "Le distributeur", formats: {
    "Vidéo": { "1A": ["Coumailleau Noa", "Harmane Adam", "Leroy Andréa"], "2A": ["Lacome Thaïs", "Trapy Milan"], "3A": ["Dupuy Lola", "Lucas Enzo"] },
    "Affiche": { "1A": ["Dudezert Enzo", "Peyrusse Mathieu"], "2A": ["Piga Chavigny Sterenn", "Williot Tom"], "3A": ["Chaix Nicolas", "Mastrantuono Evan"] },
    "Site Web": { "1A": ["Maleplate Lysandre", "Mangeret Tom"], "2A": ["Esquer Justin", "Rogam Julianne"], "3A": ["Louis Baptiste", "Basset Antoine"] }
  } },
  G09: { title: "One prise", formats: {
    "Vidéo": { "1A": ["Mortreuil Anaïs", "Brieux Noah"], "2A": ["Rogam Julianne", "Bonaventure-Sanchez Alvin"], "3A": ["Mastrantuono Evan", "Savary--Tolstoï Amélie"] },
    "Affiche": { "1A": ["Baille Lucas", "Plault Nathan"], "2A": ["El Azzaoui Othman", "Younes Samuel"], "3A": ["Blanquart Noé", "Lucas Enzo"] },
    "Site Web": { "1A": ["Coumailleau Noa", "Fitton Ella", "Herzi Maïa"], "2A": ["Daviaud Margaux", "Piga Chavigny Sterenn"], "3A": ["Abdallah Anis", "Tea Hélène"] }
  } },
  G10: { title: "Le AirDrop inconnu", formats: {
    "Vidéo": { "1A": ["Ayres-Beaume Allan", "Niollet--Mascarua Amaya", "Octobon Madelyn"], "2A": ["Daviaud Margaux", "Ledda Enzo"], "3A": ["Chaix Nicolas", "Blanquart Noé"] },
    "Affiche": { "1A": ["Fitton Ella", "Kontrec Louka", "Mirault Malakaï"], "2A": ["Barthes Jade", "Domarco Clément"], "3A": ["Dupuy Lola", "Taombe Yanis"] },
    "Site Web": { "1A": ["Mortreuil Anaïs", "Peyrusse Mathieu"], "2A": ["Brunet Ethan", "Liessi Lily"], "3A": ["Lucas Enzo"] }
  } },
  G11: { title: "Le message supprimé", formats: {
    "Vidéo": { "1A": ["Castex Manon", "Benchabane-Vallot Louane"], "2A": ["Brunet Ethan", "Attou Yanis"], "3A": ["Duconseil Charlotte", "Taombe Yanis"] },
    "Affiche": { "1A": ["Levy Jelly", "Niollet--Mascarua Amaya"], "2A": ["Rgana El Hami Yassmine", "Sim Loana"], "3A": ["Duclos Mathis", "Savary--Tolstoï Amélie"] },
    "Site Web": { "1A": ["Chevallier Enzo", "Laborieux Eloïse"], "2A": ["Younes Samuel", "Beauchamp Eliott"], "3A": ["Chaix Nicolas", "Dupuy Lola"] }
  } },
  G12: { title: "L’appel inconnu", formats: {
    "Vidéo": { "1A": ["Araujo Emma", "Mahhad Ilyess"], "2A": ["Piga Chavigny Sterenn", "Younes Samuel"], "3A": ["Duclos Mathis", "Yvelin Pauline"] },
    "Affiche": { "1A": ["Bey Sofiane", "Leroy Andréa", "Reiss-Barde Mylo"], "2A": ["Brunet Ethan", "Rogam Julianne"], "3A": ["Abdallah Anis", "Pasticier Lucas"] },
    "Site Web": { "1A": ["Niollet--Mascarua Amaya", "Plault Nathan", "Verdier Tinéo"], "2A": ["Lotf Khalid", "Trapy Milan"], "3A": ["Mastrantuono Evan", "Nobilet Ywan"] }
  } },
  G13: { title: "« J’arrive dans 5 minutes »", formats: {
    "Vidéo": { "1A": ["Fitton Ella", "Gautelier Dorian", "Marzouk Salma"], "2A": ["Frances Mattéo", "Achbli Hajar"], "3A": ["Astabie Margaux", "Basset Antoine"] },
    "Affiche": { "1A": ["Mongo Malcom", "Plotkin Froger Tao"], "2A": ["Daviaud Margaux", "Trapy Milan"], "3A": ["Louis Baptiste", "Tran-Ky Éric"] },
    "Site Web": { "1A": ["Delatre Isaac", "Laud Lakisha", "Leroy Andréa"], "2A": ["Domarco Clément", "Ben Si Ali Maryam"], "3A": ["Duclos Mathis", "Blanquart Noé"] }
  } },
  G14: { title: "Le raccourci qui prend plus de temps", formats: {
    "Vidéo": { "1A": ["Peyrusse Mathieu", "Plault Nathan"], "2A": ["Domarco Clément", "Picard Manon"], "3A": ["Louis Baptiste", "Abdallah Anis"] },
    "Affiche": { "1A": ["Coumailleau Noa", "Mortreuil Anaïs", "Yapmis Umut"], "2A": ["Gogan Cydji", "Martone Helio"], "3A": ["Basset Antoine", "Oudart Matthias"] },
    "Site Web": { "1A": ["Ecotière Paul", "Munier Gauthier"], "2A": ["Messean Théo", "Pauly Paul"], "3A": ["Savary--Tolstoï Amélie", "Taombe Yanis"] }
  } },
  G15: { title: "La porte qu’on tient", formats: {
    "Vidéo": { "1A": ["Plotkin Froger Tao", "Reiss-Barde Mylo", "Yapmis Umut"], "2A": ["Rudenko Viktoriia", "Sim Loana"], "3A": ["Jamelot Lana", "Leclercq Tom"] },
    "Affiche": { "1A": ["Chevallier Enzo", "Dorkeld Flavie", "Maleplate Lysandre"], "2A": ["Messean Théo", "Ben Si Ali Maryam"], "3A": ["Nogueras Baptiste"] },
    "Site Web": { "1A": ["Castex Manon", "Harmane Adam"], "2A": ["Achbli Hajar", "Picard Manon", "Wilson Maïlyse"], "3A": ["Larue Maël", "Le Roho Lorenzo"] }
  } },
  G16: { title: "La chaise parfaite", formats: {
    "Vidéo": { "1A": ["Dudezert Enzo", "Mirault Malakaï", "Bey Sofiane"], "2A": ["Petit Martin", "Rgana El Hami Yassmine"], "3A": ["Rakotoarijaona Pamela", "Rejimand Sarah"] },
    "Affiche": { "1A": ["Ecotière Paul", "Harbil Amin"], "2A": ["Esquer Justin", "Lotf Khalid"], "3A": ["Larue Maël", "Tea Hélène"] },
    "Site Web": { "1A": ["Benchabane-Vallot Louane", "Mahhad Ilyess", "Raigne-Nicolas Jérémi"], "2A": ["Berger Louise", "Ledda Enzo"], "3A": ["Astabie Margaux", "Nogueras Baptiste"] }
  } },
  G17: { title: "La pause clope sans clope", formats: {
    "Vidéo": { "1A": ["Balester Tom", "Hauvel Clémence"], "2A": ["Beauchamp Eliott", "Esquer Justin", "Liessi Lily"], "3A": ["Detaevernier Tom", "Elbs Linda"] },
    "Affiche": { "1A": ["Fernandes Thomas", "Finucci Romain"], "2A": ["Donato-Derouen Macsven", "Frances Mattéo", "Soulard Remi"], "3A": ["Duconseil Charlotte", "Yvelin Pauline"] },
    "Site Web": { "1A": ["Dudezert Enzo", "Reiss-Barde Mylo"], "2A": ["Bruno Lisa", "El Azzaoui Othman", "Barthes Jade"], "3A": ["Tcha Jordan", "Tran-Ky Éric"] }
  } },
  G18: { title: "« Ça prend 5 minutes »", formats: {
    "Vidéo": { "1A": ["Levy Jelly", "Mongo Malcom"], "2A": ["Barthes Jade", "Wilson Maïlyse"], "3A": ["Nogueras Baptiste", "Oudart Matthias"] },
    "Affiche": { "1A": ["Laud Lakisha", "Raigne-Nicolas Jérémi", "Verdier Tinéo"], "2A": ["Pauly Paul", "Rudenko Viktoriia"], "3A": ["Le Roho Lorenzo", "Rakotoarijaona Pamela"] },
    "Site Web": { "1A": ["Brieux Noah", "Gautelier Dorian", "Harbil Amin"], "2A": ["Lacome Thaïs", "Petit Martin"], "3A": ["Duconseil Charlotte", "Leclercq Tom"] }
  } },
  G19: { title: "FINAL_v2_def_BON", formats: {
    "Vidéo": { "1A": ["Dorkeld Flavie", "Harbil Amin"], "2A": ["Gogan Cydji", "Williot Tom"], "3A": ["Le Roho Lorenzo", "Tran-Ky Éric"] },
    "Affiche": { "1A": ["Delatre Isaac", "Laborieux Eloïse", "Mangeret Tom"], "2A": ["Liessi Lily", "Wilson Maïlyse"], "3A": ["Leclercq Tom", "Rejimand Sarah"] },
    "Site Web": { "1A": ["Araujo Emma", "Ayres-Beaume Allan"], "2A": ["Bonaventure-Sanchez Alvin", "Frances Mattéo"], "3A": ["Jamelot Lana", "Rakotoarijaona Pamela"] }
  } },
  G20: { title: "La typo interdite", formats: {
    "Vidéo": { "1A": ["Kontrec Louka", "Raigne-Nicolas Jérémi"], "2A": ["El Azzaoui Othman", "Martone Helio"], "3A": ["Larue Maël", "Pasticier Lucas"] },
    "Affiche": { "1A": ["Herzi Maïa", "Munier Gauthier"], "2A": ["Petit Martin", "Beauchamp Eliott"], "3A": ["Jamelot Lana", "Nobilet Ywan"] },
    "Site Web": { "1A": ["Dorkeld Flavie", "Marzouk Salma", "Octobon Madelyn"], "2A": ["Attou Yanis", "Rudenko Viktoriia"], "3A": ["Rejimand Sarah", "Yvelin Pauline"] }
  } }
};

const projects = [
  { group: "G01", title: "Gavé", pagePath: "G01/siteweb_G01/index.html" },
  { group: "G02", title: "Rive Droite vs Rive Gauche", pagePath: "G02/siteweb_G02/index.html" },
  { group: "G03", title: "Le Batcub", pagePath: "G03/siteweb_G03/dist/client/index.html" },
  { group: "G04", title: "Le miroir d’eau sans eau", pagePath: "G04/siteweb_G04/index.html" },
  { group: "G05", title: "La gourde oubliée", pagePath: "G05/siteweb_G05/index.html" },
  { group: "G06", title: "Le choix du resto à midi", pagePath: "G06/siteweb_G06/index.html" },
  { group: "G07", title: "Le café à 40 centimes", pagePath: "G07/siteweb_G07/index.html" },
  { group: "G08", title: "Le distributeur", pagePath: "G08/siteweb_G08/index.html" },
  { group: "G09", title: "One prise", pagePath: "G09/siteweb_G09/index.html" },
  { group: "G10", title: "Le AirDrop inconnu", pagePath: "G10/siteweb_G10/index.html" },
  { group: "G11", title: "Le message supprimé", pagePath: "G11/siteweb_G11/index.html" },
  { group: "G12", title: "L’appel inconnu", pagePath: "G12/siteweb_G12/index.html" },
  { group: "G13", title: "« J’arrive dans 5 minutes »", pagePath: "G13/siteweb_G13/index.html" },
  { group: "G14", title: "Le raccourci qui prend plus de temps", pagePath: "G14/siteweb_G14/index.html" },
  { group: "G15", title: "La porte qu’on tient", pagePath: "G15/GRP15_site_web/dist/index.html" },
  { group: "G16", title: "La chaise parfaite", pagePath: "G16/siteweb_G16/index.html" },
  { group: "G17", title: "La pause clope sans clope", pagePath: "G17/Siteweb_G17/index.html" },
  { group: "G18", title: "« Ça prend 5 minutes »", pagePath: "G18/siteweb_G18/index.html" },
  { group: "G19", title: "FINAL_v2_def_BON", pagePath: "G19/siteweb_G19/index.html" },
  { group: "G20", title: "La typo interdite", pagePath: "G20/site_web_G20/index.html" }
];

const grid = document.getElementById("projectGrid");

function escapeHtml(value) {
  return value
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function mediaFolders(group, siteFolder) {
  const actualSite = siteFolder.replace(/\/dist(?:\/client)?$/, "");
  return [...new Set([group, `${group}/siteweb_${group}`, actualSite])];
}
function buildPosterCandidates(group, siteFolder) {
  const rootCandidates = ["webp", "png", "jpg", "jpeg", "pdf"].map(extension => `${group}/affiche_${group}.${extension}`);
  const siteCandidates = mediaFolders(group, siteFolder).flatMap(folder =>
    ["webp", "png", "jpg", "jpeg", "pdf"].map(extension => `${folder}/assets/images/affiche_${group}.${extension}`)
  );
  return [...new Set([...rootCandidates, ...siteCandidates])];
}
function buildVideoCandidates(group, siteFolder) {
  return mediaFolders(group, siteFolder).flatMap(folder =>
    ["mp4", "mov"].map(extension => `${folder}/assets/videos/video_${group}.${extension}`)
  );
}
function getYouTubeEmbedUrl(videoUrl) {
  if (!videoUrl) return null;
  const iframeMatch = videoUrl.match(/src="([^"]+)"/i);
  if (iframeMatch) return iframeMatch[1];
  const match = videoUrl.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/i);
  return match ? `https://www.youtube.com/embed/${match[1]}?autoplay=1&rel=0` : null;
}

const mediaDialog = document.getElementById("mediaDialog");
const mediaContent = document.getElementById("mediaContent");
const creditsDialog = document.getElementById("creditsDialog");
const creditsContent = document.getElementById("creditsContent");
let mediaRequest = 0;
let previousOverflow = "";

function openCredits(group, title) {
  const info = projectCredits[group] || { title, formats: { "Vidéo": { "1A": [], "2A": [], "3A": [] }, "Affiche": { "1A": [], "2A": [], "3A": [] }, "Site Web": { "1A": [], "2A": [], "3A": [] } } };
  document.getElementById("creditsTitle").textContent = `${group} · ${info.title || title}`;
  creditsContent.replaceChildren();

  const formatLabels = ["Vidéo", "Affiche", "Site Web"];
  const grid = document.createElement("div");
  grid.className = "credits-grid";

  for (const formatLabel of formatLabels) {
    const formatColumn = document.createElement("div");
    formatColumn.className = "credits-format-column";

    const formatHeading = document.createElement("h3");
    formatHeading.textContent = formatLabel;
    formatColumn.appendChild(formatHeading);

    const yearGroups = ["1A", "2A", "3A"];
    for (const year of yearGroups) {
      const yearGroup = document.createElement("div");
      yearGroup.className = "credits-year-group";

      const yearHeading = document.createElement("h4");
      yearHeading.textContent = year;
      yearGroup.appendChild(yearHeading);

      const list = document.createElement("ul");
      const names = info.formats?.[formatLabel]?.[year] || [];
      for (const name of names) {
        if (!name || !String(name).trim()) continue;
        const item = document.createElement("li");
        item.textContent = name;
        list.appendChild(item);
      }
      if (!list.children.length) {
        const empty = document.createElement("li");
        empty.textContent = "Aucune donnée";
        list.appendChild(empty);
      }
      yearGroup.appendChild(list);
      formatColumn.appendChild(yearGroup);
    }

    grid.appendChild(formatColumn);
  }

  creditsContent.appendChild(grid);

  previousOverflow = document.body.style.overflow;
  document.body.style.overflow = "hidden";
  creditsDialog.showModal();
}

document.getElementById("creditsClose").addEventListener("click", () => {
  creditsDialog.close();
  document.body.style.overflow = previousOverflow;
});
creditsDialog.addEventListener("close", () => {
  document.body.style.overflow = previousOverflow;
});

function findMedia(candidates) {
  return window.GroupMedia.findCandidates(candidates);
}

function clearMedia() {
  mediaContent.querySelector("video")?.pause();
  mediaContent.replaceChildren();
}

mediaDialog.addEventListener("close", () => {
  mediaRequest++;
  clearMedia();
  document.body.style.overflow = previousOverflow;
});
document.getElementById("mediaClose").addEventListener("click", () => mediaDialog.close());
mediaDialog.addEventListener("click", event => {
  if (event.target === mediaDialog) {
    const box = mediaDialog.getBoundingClientRect();
    if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) mediaDialog.close();
  }
});

async function openMedia(kind, group, title, siteFolder) {
  const request = ++mediaRequest;
  clearMedia();
  document.getElementById("mediaTitle").textContent = `${kind === "video" ? "Vidéo" : "Affiche"} — ${group} · ${title}`;
  mediaContent.textContent = "Chargement…";
  if (!mediaDialog.open) {
    previousOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    mediaDialog.showModal();
  }

  const youtubeHtml = youtubeVideos[group];
  const youtubeUrl = getYouTubeEmbedUrl(youtubeHtml || "");
  const candidates = kind === "video"
    ? buildVideoCandidates(group, siteFolder)
    : buildPosterCandidates(group, siteFolder);

  let src = null;
  if (kind === "video" && youtubeHtml) {
    src = youtubeUrl || youtubeHtml;
  } else {
    src = await findMedia(candidates);
  }

  if (request !== mediaRequest || !mediaDialog.open) return;
  mediaContent.replaceChildren();
  if (!src) {
    mediaContent.textContent = location.protocol === "file:"
      ? "Le navigateur ne peut pas détecter ce fichier local automatiquement."
      : `${kind === "video" ? "La vidéo" : "L’affiche"} de ${group} n’est pas encore disponible.`;
    if (location.protocol === "file:") {
      const note = document.createElement("p");
      note.textContent = "Vous pouvez aussi ouvrir directement le fichier :";
      mediaContent.appendChild(note);
      for (const candidate of candidates) {
        const link = document.createElement("a");
        link.href = candidate; link.target = "_blank"; link.rel = "noopener noreferrer";
        link.textContent = candidate.split('/').pop();
        mediaContent.appendChild(link);
      }
    }
    return;
  }

  let media;
  if (kind === "video") {
    if (youtubeHtml) {
      const wrapper = document.createElement("div");
      wrapper.className = "youtube-frame-wrapper";
      wrapper.innerHTML = youtubeHtml;
      media = wrapper.firstElementChild;
      media.setAttribute("width", "100%");
      media.setAttribute("height", "100%");
      media.style.aspectRatio = "16 / 9";
      media.style.width = "100%";
      media.style.height = "100%";
      media.style.maxHeight = "70dvh";
      media.style.display = "block";
      media.style.margin = "0 auto";
      media.style.border = "0";
      media.style.borderRadius = "16px";
    } else {
      media = document.createElement("video");
      media.controls = true;
      media.playsInline = true;
      media.preload = "metadata";
      media.src = src;
    }
  } else if (src.endsWith(".pdf")) {
    media = document.createElement("iframe");
    media.title = `Affiche PDF de ${group}`;
    media.src = src;
  } else {
    media = document.createElement("img");
    media.alt = `Affiche de ${group} — ${title}`;
    media.src = src;
  }

  mediaContent.appendChild(media);
  const link = document.createElement("a");
  link.href = youtubeUrl || src;
  link.target = "_blank";
  link.rel = "noopener noreferrer";
  link.textContent = kind === "video" && youtubeHtml
    ? "Ouvrir la vidéo YouTube ↗"
    : "Ouvrir le fichier dans un nouvel onglet ↗";
  mediaContent.appendChild(link);

  if (kind !== "video" || !youtubeHtml) {
    media.addEventListener("error", () => {
      const message = document.createElement("p");
      message.textContent = "Ce fichier ne peut pas être affiché dans ce navigateur. Vous pouvez l’ouvrir avec le lien ci-dessous.";
      media.replaceWith(message);
    }, { once: true });
  }

  if (kind === "video" && !youtubeUrl) media.play().catch(() => {});
}

projects.forEach(({ group, title, pagePath }, index) => {
  const siteFolder = pagePath.replace(/\/index\.html$/i, "").replace(/\/[^/]+\.html$/i, "");
  const card = document.createElement("article");
  card.className = "project-card";
  card.classList.add("is-pending");
  card.style.transitionDelay = `${Math.min(index % 4, 3) * 75}ms`;
  card.setAttribute("aria-label", `Projet ${group} : ${title}`);

  const poster = document.createElement("div");
  poster.className = "poster";

  poster.innerHTML = `
    <span class="project-number">${group}</span>

  `;

  const actions = document.createElement("div");
  actions.className = "project-actions";
  const visit = document.createElement("a");
  visit.href = pagePath;
  visit.target = "_blank";
  visit.rel = "noopener noreferrer";
  visit.textContent = "Visiter le site ↗";
  visit.setAttribute("aria-label", `Visiter le site de ${group}`);
  actions.appendChild(visit);
  for (const [kind, label] of [["poster", "Afficher l’affiche"], ["video", "Voir la vidéo"], ["credits", "Crédits"]]) {
    const button = document.createElement("button");
    button.type = "button";
    button.textContent = label;
    button.setAttribute("aria-label", `${label} — ${group}`);
    button.setAttribute("aria-haspopup", kind === "credits" ? "dialog" : "dialog");
    button.addEventListener("click", () => {
      if (kind === "credits") {
        openCredits(group, title);
        return;
      }
      openMedia(kind, group, title, siteFolder);
    });
    actions.appendChild(button);
  }
  poster.appendChild(actions);

  const image = document.createElement("img");
  image.alt = `Affiche du projet ${group} — ${title}`;
  image.loading = index < 4 ? "eager" : "lazy";
  image.decoding = "async";

  const candidates = buildPosterCandidates(group, siteFolder);
  let candidateIndex = 0;
  let currentMedia = null;

  function appendMedia(element) {
    if (currentMedia && currentMedia !== element) {
      currentMedia.remove();
    }

    poster.appendChild(element);
    currentMedia = element;
  }

  async function tryNext() {
    if (candidateIndex >= candidates.length) {
      poster.insertAdjacentHTML(
        "beforeend",
        `<div style="
          position:absolute;inset:0;display:grid;place-items:center;
          padding:30px;text-align:center;background:#C0ADD8;color:#530096;
          font-weight:800;">
          ${escapeHtml(group)}<br>Affiche à venir
        </div>`
      );
      return;
    }

    const src = candidates[candidateIndex++];
    const extension = src.split(".").pop().toLowerCase();
    const isPdf = extension === "pdf";
    const isVideo = ["mp4", "mov", "webm", "m4v"].includes(extension);

    if (isPdf) {
      try {
        const response = await fetch(src, { method: "HEAD" });
        if (!response.ok || response.headers.get("content-type")?.includes("text/html")) return tryNext();
      } catch { return tryNext(); }
      const frame = document.createElement("iframe");
      frame.className = "pdf-poster";
      frame.title = `Affiche PDF du projet ${group}`;
      frame.tabIndex = -1;
      frame.src = src;
      frame.addEventListener("error", tryNext);
      appendMedia(frame);
      return;
    }

    if (isVideo) {
      const video = document.createElement("video");
      video.className = "video-poster";
      video.src = src;
      video.muted = true;
      video.playsInline = true;
      video.autoplay = true;
      video.loop = true;
      video.preload = "metadata";
      video.setAttribute("aria-label", `Affiche vidéo du projet ${group}`);
      video.addEventListener("error", tryNext);
      appendMedia(video);
      video.play().catch(() => {});
      return;
    }

    image.src = src;
    appendMedia(image);
  }

  image.addEventListener("error", tryNext);
  tryNext();

  const titleElement = document.createElement("h3");
  titleElement.className = "project-title";
  titleElement.textContent = title;

  card.appendChild(poster);
  card.appendChild(titleElement);
  grid.appendChild(card);
});

const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

if (!prefersReducedMotion) {
  const cardObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add("is-visible");
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.12 });

  document.querySelectorAll(".project-card").forEach((card) => cardObserver.observe(card));

  const parallaxTargets = [
    [document.querySelector(".background video"), 0.035],
    [document.querySelector(".aurora-one"), -0.025],
    [document.querySelector(".aurora-two"), 0.045],
    [document.querySelector(".section-orb-one"), -0.04],
    [document.querySelector(".section-orb-two"), 0.03]
  ];

  let ticking = false;
  function updateParallax() {
    const scrollY = window.scrollY;
    parallaxTargets.forEach(([element, speed]) => {
      if (element) element.style.translate = `0 ${scrollY * speed}px`;
    });
    ticking = false;
  }

  window.addEventListener("scroll", () => {
    if (!ticking) {
      window.requestAnimationFrame(updateParallax);
      ticking = true;
    }
  }, { passive: true });
  updateParallax();
} else {
  document.querySelectorAll(".project-card").forEach((card) => card.classList.add("is-visible"));
}

const video = document.getElementById("backgroundVideo");
if (video) {
  video.play().catch(() => {
    // L'attribut muted + playsinline devrait normalement permettre l'autoplay.
  });
}
