-- 20151010 Version 11
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres
--

CREATE PROCEDURAL LANGUAGE plpgsql;


ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO postgres;

SET search_path = public, pg_catalog;

create user nnuser with password 'temppass';
alter user nnuser with login;
create user ronnuser with password 'temppass';
alter user ronnuser with login;
create tablespace nnts owner nnuser location '/data/pgsql/nndata2';
create database nnbase2 owner nnuser tablespace nnts;

\c nnbase2

--
-- Name: perfdata_insert(); Type: FUNCTION; Schema: public; Owner: nnuser
--

CREATE FUNCTION perfdata_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  if ((date(NEW.daytime)-date'1970-01-01') % 16 = 0 ) then
    insert into perfdata_0 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 1 ) then
    insert into perfdata_1 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 2 ) then
    insert into perfdata_2 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 3 ) then
    insert into perfdata_3 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 4 ) then
    insert into perfdata_4 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 5 ) then
    insert into perfdata_5 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 6 ) then
    insert into perfdata_6 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 7 ) then
    insert into perfdata_7 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 8 ) then
    insert into perfdata_8 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 9 ) then
    insert into perfdata_9 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 10 ) then
    insert into perfdata_10 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 11 ) then
    insert into perfdata_11 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 12 ) then
    insert into perfdata_12 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 13 ) then
    insert into perfdata_13 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 14 ) then
    insert into perfdata_14 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 16 = 15 ) then
    insert into perfdata_15 values (new.*);
  end if;
  return null;
end;
$$;


ALTER FUNCTION public.perfdata_insert() OWNER TO nnuser;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: hosts; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE hosts (
    host character varying NOT NULL,
    os character varying,
    lparname character varying,
    serial character varying,
    vio numeric(1,0)
);


ALTER TABLE public.hosts OWNER TO nnuser;

--
-- Name: nnusers; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE nnusers (
    nnuser character varying
);


ALTER TABLE public.nnusers OWNER TO nnuser;

--
-- Name: parameter; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE parameter (
    param character varying,
    value character varying
);


ALTER TABLE public.parameter OWNER TO nnuser;

--
-- Name: perfdata; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata (
    host character varying NOT NULL,
    topic character varying NOT NULL,
    metric character varying NOT NULL,
    value numeric,
    daytime timestamp without time zone NOT NULL
);


ALTER TABLE public.perfdata OWNER TO nnuser;

--
-- Name: perfdata_0; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_0 (CONSTRAINT perfdata_0_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 0))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_0 OWNER TO nnuser;

--
-- Name: perfdata_1; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_1 (CONSTRAINT perfdata_1_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 1))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_1 OWNER TO nnuser;

--
-- Name: perfdata_2; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_2 (CONSTRAINT perfdata_2_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 2))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_2 OWNER TO nnuser;

--
-- Name: perfdata_3; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_3 (CONSTRAINT perfdata_3_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 3))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_3 OWNER TO nnuser;

--
-- Name: perfdata_4; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_4 (CONSTRAINT perfdata_4_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 4))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_4 OWNER TO nnuser;

--
-- Name: perfdata_5; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_5 (CONSTRAINT perfdata_5_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 5))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_5 OWNER TO nnuser;

--
-- Name: perfdata_6; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_6 (CONSTRAINT perfdata_6_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 6))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_6 OWNER TO nnuser;

--
-- Name: perfdata_7; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_7 (CONSTRAINT perfdata_7_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 7))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_7 OWNER TO nnuser;

--
-- Name: perfdata_8; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_8 (CONSTRAINT perfdata_8_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 8))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_8 OWNER TO nnuser;

--
-- Name: perfdata_9; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_9 (CONSTRAINT perfdata_9_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 9))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_9 OWNER TO nnuser;

--
-- Name: perfdata_10; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_10 (CONSTRAINT perfdata_10_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 10))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_10 OWNER TO nnuser;

--
-- Name: perfdata_11; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_11 (CONSTRAINT perfdata_11_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 11))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_11 OWNER TO nnuser;

--
-- Name: perfdata_12; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_12 (CONSTRAINT perfdata_12_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 12))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_12 OWNER TO nnuser;

--
-- Name: perfdata_13; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_13 (CONSTRAINT perfdata_13_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 13))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_13 OWNER TO nnuser;

--
-- Name: perfdata_14; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_14 (CONSTRAINT perfdata_14_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 14))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_14 OWNER TO nnuser;

--
-- Name: perfdata_15; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_15 (CONSTRAINT perfdata_15_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 16) = 15))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_15 OWNER TO nnuser;



--
-- Name: perfsum; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfsum (
    host character varying NOT NULL,
    date character varying NOT NULL,
    avgcpu numeric,
    avgcpu9_18 numeric,
    avgmem numeric
);


ALTER TABLE public.perfsum OWNER TO nnuser;

--
-- Name: perfsum2; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfsum2 (
    host character varying,
    date character varying,
    avgcpu numeric,
    avgcpu9_18 numeric,
    avgmem numeric
);


ALTER TABLE public.perfsum2 OWNER TO nnuser;

--
-- Name: hosts_pkey; Type: CONSTRAINT; Schema: public; Owner: nnuser; Tablespace:
--

ALTER TABLE ONLY hosts
    ADD CONSTRAINT hosts_pkey PRIMARY KEY (host);


--
-- Name: perfsum_pkey; Type: CONSTRAINT; Schema: public; Owner: nnuser; Tablespace:
--

ALTER TABLE ONLY perfsum
    ADD CONSTRAINT perfsum_pkey PRIMARY KEY (host, date);


--
-- Name: indx_perfdata_0_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_0_htmd ON perfdata_0 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_1_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_1_htmd ON perfdata_1 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_2_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_2_htmd ON perfdata_2 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_3_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_3_htmd ON perfdata_3 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_4_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_4_htmd ON perfdata_4 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_5_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_5_htmd ON perfdata_5 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_6_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_6_htmd ON perfdata_6 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_7_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_7_htmd ON perfdata_7 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_8_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_8_htmd ON perfdata_8 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_9_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_9_htmd ON perfdata_9 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_10_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_10_htmd ON perfdata_10 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_11_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_11_htmd ON perfdata_11 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_12_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_12_htmd ON perfdata_12 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_13_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_13_htmd ON perfdata_13 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_14_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_14_htmd ON perfdata_14 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_15_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_15_htmd ON perfdata_15 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_htmd ON perfdata USING btree (host, topic, metric, daytime);


--
-- Name: insert_perfdata_trigger; Type: TRIGGER; Schema: public; Owner: nnuser
--

CREATE TRIGGER insert_perfdata_trigger
    BEFORE INSERT ON perfdata
    FOR EACH ROW
    EXECUTE PROCEDURE perfdata_insert();


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: hosts; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE hosts FROM PUBLIC;
REVOKE ALL ON TABLE hosts FROM nnuser;
GRANT ALL ON TABLE hosts TO nnuser;
GRANT SELECT ON TABLE hosts TO ronnuser;


--
-- Name: nnusers; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE nnusers FROM PUBLIC;
REVOKE ALL ON TABLE nnusers FROM nnuser;
GRANT ALL ON TABLE nnusers TO nnuser;
GRANT SELECT ON TABLE nnusers TO ronnuser;


--
-- Name: parameter; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE parameter FROM PUBLIC;
REVOKE ALL ON TABLE parameter FROM nnuser;
GRANT ALL ON TABLE parameter TO nnuser;
GRANT SELECT ON TABLE parameter TO ronnuser;


--
-- Name: perfdata; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata FROM PUBLIC;
REVOKE ALL ON TABLE perfdata FROM nnuser;
GRANT ALL ON TABLE perfdata TO nnuser;
GRANT SELECT ON TABLE perfdata TO ronnuser;


--
-- Name: perfdata_0; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_0 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_0 FROM nnuser;
GRANT ALL ON TABLE perfdata_0 TO nnuser;
GRANT SELECT ON TABLE perfdata_0 TO ronnuser;


--
-- Name: perfdata_1; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_1 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_1 FROM nnuser;
GRANT ALL ON TABLE perfdata_1 TO nnuser;
GRANT SELECT ON TABLE perfdata_1 TO ronnuser;


--
-- Name: perfdata_2; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_2 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_2 FROM nnuser;
GRANT ALL ON TABLE perfdata_2 TO nnuser;
GRANT SELECT ON TABLE perfdata_2 TO ronnuser;


--
-- Name: perfdata_3; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_3 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_3 FROM nnuser;
GRANT ALL ON TABLE perfdata_3 TO nnuser;
GRANT SELECT ON TABLE perfdata_3 TO ronnuser;

--
-- Name: perfdata_4; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_4 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_4 FROM nnuser;
GRANT ALL ON TABLE perfdata_4 TO nnuser;
GRANT SELECT ON TABLE perfdata_4 TO ronnuser;

--
-- Name: perfdata_5; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_5 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_5 FROM nnuser;
GRANT ALL ON TABLE perfdata_5 TO nnuser;
GRANT SELECT ON TABLE perfdata_5 TO ronnuser;

--
-- Name: perfdata_6; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_6 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_6 FROM nnuser;
GRANT ALL ON TABLE perfdata_6 TO nnuser;
GRANT SELECT ON TABLE perfdata_6 TO ronnuser;

--
-- Name: perfdata_7; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_7 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_7 FROM nnuser;
GRANT ALL ON TABLE perfdata_7 TO nnuser;
GRANT SELECT ON TABLE perfdata_7 TO ronnuser;

--
-- Name: perfdata_8; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_8 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_8 FROM nnuser;
GRANT ALL ON TABLE perfdata_8 TO nnuser;
GRANT SELECT ON TABLE perfdata_8 TO ronnuser;

--
-- Name: perfdata_9; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_9 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_9 FROM nnuser;
GRANT ALL ON TABLE perfdata_9 TO nnuser;
GRANT SELECT ON TABLE perfdata_9 TO ronnuser;

--
-- Name: perfdata_10; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_10 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_10 FROM nnuser;
GRANT ALL ON TABLE perfdata_10 TO nnuser;
GRANT SELECT ON TABLE perfdata_10 TO ronnuser;

--
-- Name: perfdata_11; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_11 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_11 FROM nnuser;
GRANT ALL ON TABLE perfdata_11 TO nnuser;
GRANT SELECT ON TABLE perfdata_11 TO ronnuser;

--
-- Name: perfdata_12; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_12 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_12 FROM nnuser;
GRANT ALL ON TABLE perfdata_12 TO nnuser;
GRANT SELECT ON TABLE perfdata_12 TO ronnuser;

--
-- Name: perfdata_13; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_13 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_13 FROM nnuser;
GRANT ALL ON TABLE perfdata_13 TO nnuser;
GRANT SELECT ON TABLE perfdata_13 TO ronnuser;

--
-- Name: perfdata_14; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_14 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_14 FROM nnuser;
GRANT ALL ON TABLE perfdata_14 TO nnuser;
GRANT SELECT ON TABLE perfdata_14 TO ronnuser;

--
-- Name: perfdata_15; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_15 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_15 FROM nnuser;
GRANT ALL ON TABLE perfdata_15 TO nnuser;
GRANT SELECT ON TABLE perfdata_15 TO ronnuser;


--
-- Name: perfsum; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfsum FROM PUBLIC;
REVOKE ALL ON TABLE perfsum FROM nnuser;
GRANT ALL ON TABLE perfsum TO nnuser;
GRANT SELECT ON TABLE perfsum TO ronnuser;


--
-- Name: perfsum2; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfsum2 FROM PUBLIC;
REVOKE ALL ON TABLE perfsum2 FROM nnuser;
GRANT ALL ON TABLE perfsum2 TO nnuser;
GRANT SELECT ON TABLE perfsum2 TO ronnuser;


--
-- PostgreSQL database dump complete
--
