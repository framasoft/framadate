--==========================================================================
--
--Université de Strasbourg - Direction Informatique
--Auteur : Guilhem BORGHESI
--Création : Février 2008
--
--borghesi@unistra.fr
--
--Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
--respectant les principes de diffusion des logiciels libres. Vous pouvez
--utiliser, modifier et/ou redistribuer ce programme sous les conditions
--de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA 
--sur le site "http://www.cecill.info".
--
--Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
--pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
--termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
--
--==========================================================================
--
--Université de Strasbourg - Direction Informatique
--Author : Guilhem BORGHESI
--Creation : Feb 2008
--
--borghesi@unistra.fr
--
--This software is governed by the CeCILL-B license under French law and
--abiding by the rules of distribution of free software. You can  use, 
--modify and/ or redistribute the software under the terms of the CeCILL-B
--license as circulated by CEA, CNRS and INRIA at the following URL
--"http://www.cecill.info". 
--
--The fact that you are presently reading this means that you have had
--knowledge of the CeCILL-B license and that you accept its terms. You can
--find a copy of this license in the file LICENSE.
--
--==========================================================================

--
-- PostgreSQL database dump
--

--
-- Name: sondage; Type: TABLE;
--

CREATE TABLE sondage (
    id_sondage text NOT NULL,
    commentaires text,
    mail_admin text,
    nom_admin text,
    titre text,
    id_sondage_admin text,
    date_fin text,
    format text,
    mailsonde text
);


--
-- Name: sujet_studs; Type: TABLE;
--

CREATE TABLE sujet_studs (
    id_sondage text,
    sujet text
);


--
-- Name: user_studs; Type: TABLE;
--

CREATE TABLE user_studs (
    nom text,
    id_sondage text,
    reponses text,
    id_users serial NOT NULL
);


--
-- Name: comments; Type: TABLE;
--

CREATE TABLE comments (
    id_sondage text,
    comment text,
    usercomment text,
    id_comment serial NOT NULL
);

CREATE OR REPLACE FUNCTION from_unixtime(integer) RETURNS timestamp AS
'SELECT $1::abstime::timestamp without time zone AS result' LANGUAGE 'SQL';

--
-- Data for Name: sondage; Type: TABLE DATA;
--

COPY sondage (id_sondage, commentaires, mail_admin, nom_admin, titre, id_sondage_admin, date_fin, format, mailsonde) FROM stdin;
aqg259dth55iuhwm	Repas de Noel du service	Stephanie@saillard.com	Stephanie	Repas de Noel	aqg259dth55iuhwmy9d8jlwk	1627100361	D+	
\.


--
-- Data for Name: sujet_studs; Type: TABLE DATA;
--

COPY sujet_studs (id_sondage, sujet) FROM stdin;
aqg259dth55iuhwm	1225839600@12h,1225839600@19h,1226012400@12h,1226012400@19h,1226876400@12h,1226876400@19h,1227049200@12h,1227049200@19h,1227826800@12h,1227826800@19h
\.


--
-- Data for Name: user_studs; Type: TABLE DATA;
--

COPY user_studs (nom, id_sondage, reponses, id_users) FROM stdin;
marcel	aqg259dth55iuhwm	0110111101	933
paul	aqg259dth55iuhwm	1011010111	935
sophie	aqg259dth55iuhwm	1110110000	945
barack	aqg259dth55iuhwm	0110000	948
takashi	aqg259dth55iuhwm	0000110100	951
albert	aqg259dth55iuhwm	1010110	975
alfred	aqg259dth55iuhwm	0110010	1135
marcs	aqg259dth55iuhwm	0100001010	1143
laure	aqg259dth55iuhwm	0011000	1347
benda	aqg259dth55iuhwm	1101101100	1667
Albert	aqg259dth55iuhwm	1111110011	1668
\.


--
-- Name: sondage_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY sondage
    ADD CONSTRAINT sondage_pkey PRIMARY KEY (id_sondage);


--
-- Name: user_studs_pkey; Type: CONSTRAINT;
--

ALTER TABLE ONLY user_studs
    ADD CONSTRAINT user_studs_pkey PRIMARY KEY (id_users);

