USE [master]
GO
/****** Object:  Database [GSM2]    Script Date: 2014/7/1 21:44:11 ******/
CREATE DATABASE [GSM2]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'GSM2', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL11.MSSQLSERVER\MSSQL\DATA\GSM2.mdf' , SIZE = 13312KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'GSM2_log', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL11.MSSQLSERVER\MSSQL\DATA\GSM2_log.ldf' , SIZE = 63424KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO
ALTER DATABASE [GSM2] SET COMPATIBILITY_LEVEL = 110
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [GSM2].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [GSM2] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [GSM2] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [GSM2] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [GSM2] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [GSM2] SET ARITHABORT OFF 
GO
ALTER DATABASE [GSM2] SET AUTO_CLOSE OFF 
GO
ALTER DATABASE [GSM2] SET AUTO_CREATE_STATISTICS ON 
GO
ALTER DATABASE [GSM2] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [GSM2] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [GSM2] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [GSM2] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [GSM2] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [GSM2] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [GSM2] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [GSM2] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [GSM2] SET  DISABLE_BROKER 
GO
ALTER DATABASE [GSM2] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [GSM2] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [GSM2] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [GSM2] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [GSM2] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [GSM2] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [GSM2] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [GSM2] SET RECOVERY FULL 
GO
ALTER DATABASE [GSM2] SET  MULTI_USER 
GO
ALTER DATABASE [GSM2] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [GSM2] SET DB_CHAINING OFF 
GO
ALTER DATABASE [GSM2] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [GSM2] SET TARGET_RECOVERY_TIME = 0 SECONDS 
GO
EXEC sys.sp_db_vardecimal_storage_format N'GSM2', N'ON'
GO
USE [GSM2]
GO
/****** Object:  User [users]    Script Date: 2014/7/1 21:44:11 ******/
CREATE USER [users] FOR LOGIN [users] WITH DEFAULT_SCHEMA=[dbo]
GO
ALTER ROLE [db_datareader] ADD MEMBER [users]
GO
/****** Object:  StoredProcedure [dbo].[adj_info]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[adj_info] @ID int
as
select Adj_CellID, distance
from adjcell
where CellID = @ID
GO
/****** Object:  StoredProcedure [dbo].[BTS_info]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[BTS_info] @bts_name char(30)
as
select *
from BTS
where BtsName = @bts_name
GO
/****** Object:  StoredProcedure [dbo].[Bulk_in_MS]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[Bulk_in_MS]
as
bulk insert MS from 'D:\大学\数据库实验\GSM2 网络话务分析系统\Action\tmp\01MS.csv'  
 with(   
   FIRSTROW=2,
   FIELDTERMINATOR=',',   
   ROWTERMINATOR='\n',
   FORMATFILE = 'D:\大学\数据库实验\GSM2 网络话务分析系统\Action\tmp\01MS.xml'
)  

GO
/****** Object:  StoredProcedure [dbo].[Cell_info]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[Cell_info] @ID int
as
select *
from cell
where CellID = @ID
GO
/****** Object:  StoredProcedure [dbo].[cong]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[cong] @congsdoor float,@date_start int, @time_s int, @date_end int, @time_e int
as
select CellID as 小区, DATE,TIME/10000 as 小时,sum(traff) as 小时话务量,CAST( SUM(congsnum)as float)/sum(callnum) as 小时拥塞率,SUM(thtraff)/SUM(traff) as 小时半速率话务比例
from traffic
where ((DATE > @date_start and DATE < @date_end) or 
		(DATE =  @date_start and  @date_start= @date_end and  
		TIME >= @time_s*10000 and TIME <= @time_e*10000)) or 
		(DATE =  @date_start and @date_start<>@date_end and TIME >= @time_s*10000) or 
		(DATE =  @date_end and @date_start<>@date_end and TIME <= @time_e*10000)
group by CellID, DATE,TIME/10000
having CAST( SUM(congsnum)as float)/sum(callnum) > @congsdoor
order by CellID, DATE,TIME/10000
GO
/****** Object:  StoredProcedure [dbo].[export_data]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[export_data]
@table_name varchar(30),
@output_file varchar(50)
as
begin

EXEC sp_configure 'show advanced options', 1

-- 重新配置
RECONFIGURE

-- 启用xp_cmdshell
EXEC sp_configure 'xp_cmdshell', 1

--重新配置
RECONFIGURE

DECLARE @cmd varchar(100) 
select @cmd=concat('BCP ', @table_name,' out ',@output_file, ' -c -T -t ","')

EXEC master..xp_cmdshell @cmd

--用完后,要记得将xp_cmdshell禁用(出于安全考虑)
-- 允许配置高级选项
EXEC sp_configure 'show advanced options', 1

-- 重新配置
RECONFIGURE

-- 禁用xp_cmdshell
EXEC sp_configure 'xp_cmdshell', 0

--重新配置
RECONFIGURE

end
GO
/****** Object:  StoredProcedure [dbo].[pro1]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create procedure [dbo].[pro1]
as
update traffic
	set callcongs = congsnum / callnum
update traffic
	set rate = thtraff / traff


GO
/****** Object:  StoredProcedure [dbo].[test]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create procedure [dbo].[test]
	@pID char(30),
	@pCompany char(30)
as
	select *
	from MSC
	where MscID = @pID or MscCompany = @pCompany


GO
/****** Object:  StoredProcedure [dbo].[traffic_hour]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[traffic_hour] @ID int,@date_start int, @time_s int, @date_end int, @time_e int
as
select  DATE, TIME/10000 as 小时, SUM(traff) as 小时话务量,CAST(SUM(congsnum) as float)/sum(callnum) as 小时拥塞率,SUM(traff)/AVG(nTCH) as 每线话务量
from traffic
where CellID = @ID and ((DATE > @date_start and DATE < @date_end) or 
		(DATE =  @date_start and  @date_start= @date_end and  
		TIME >= @time_s*10000 and TIME <= @time_e*10000)) or 
		(DATE =  @date_start and @date_start<>@date_end and TIME >= @time_s*10000) or 
		(DATE =  @date_end and @date_start<>@date_end and TIME <= @time_e*10000)
group by DATE, TIME/10000
order by DATE, TIME/10000
GO
/****** Object:  StoredProcedure [dbo].[traffic_min_1]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[traffic_min_1] @ID int,@date_start int,@time_s int,
@date_end int,@time_e int
as
select DATE,TIME/100 as 分钟, sum(traff) as 分钟话务量
from traffic
where CellID = @ID and ((DATE > @date_start and DATE < @date_end) or 
		(DATE =  @date_start and  @date_start= @date_end and  
		TIME >= @time_s*10000 and TIME <= @time_e*10000)) or 
		(DATE =  @date_start and @date_start<>@date_end and TIME >= @time_s*10000) or 
		(DATE =  @date_end and @date_start<>@date_end and TIME <= @time_e*10000)
group by DATE,TIME/100
order by DATE,TIME/100
GO
/****** Object:  StoredProcedure [dbo].[traffic_min_15]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[traffic_min_15] @ID int,@date_start int,@time_s int,
@date_end int,@time_e int
as
select DATE,TIME/10000 as 时,(TIME%10000)/1500 as 刻钟, sum(traff) as 刻钟话务量
from traffic
where CellID = @ID and ((DATE > @date_start and DATE < @date_end) or 
		(DATE =  @date_start and  @date_start= @date_end and  
		TIME >= @time_s*10000 and TIME <= @time_e*10000)) or 
		(DATE =  @date_start and @date_start<>@date_end and TIME >= @time_s*10000) or 
		(DATE =  @date_end and @date_start<>@date_end and TIME <= @time_e*10000)
group by DATE,TIME/10000,(TIME%10000)/1500
order by DATE,TIME/10000,(TIME%10000)/1500
GO
/****** Object:  StoredProcedure [dbo].[xx]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[xx] as
-- 允许配置高级选项
EXEC sp_configure 'show advanced options', 1


GO
/****** Object:  StoredProcedure [dbo].[xxx]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create proc [dbo].[xxx] 
@cmd varchar(50)
as
begin
print(@cmd )
print('???')
-- 允许配置高级选项
EXEC sp_configure 'show advanced options', 1

-- 重新配置
RECONFIGURE

-- 启用xp_cmdshell
EXEC sp_configure 'xp_cmdshell', 1

--重新配置
RECONFIGURE


EXEC master..xp_cmdshell 'BCP dbo.MSC format nul -f C:\ttt.xml -x -c -T'

--用完后,要记得将xp_cmdshell禁用(出于安全考虑)
-- 允许配置高级选项
EXEC sp_configure 'show advanced options', 1

-- 重新配置
RECONFIGURE

-- 禁用xp_cmdshell
EXEC sp_configure 'xp_cmdshell', 0

--重新配置
RECONFIGURE

end

GO
/****** Object:  Table [dbo].[adjcell]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[adjcell](
	[CellID] [int] NOT NULL,
	[Adj_CellID] [int] NOT NULL,
	[CellLac] [int] NULL,
	[Adj_CellLac] [int] NULL,
	[distance] [float] NULL,
 CONSTRAINT [PK_ADJCELL] PRIMARY KEY CLUSTERED 
(
	[CellID] ASC,
	[Adj_CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[Antenna]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Antenna](
	[CellID] [int] NOT NULL,
	[AntennaHigh] [int] NULL,
	[HalfPAngle] [numeric](3, 0) NULL,
	[MaxAttenuation] [numeric](3, 0) NULL,
	[Gain] [numeric](3, 0) NULL,
	[AntTilt] [numeric](3, 0) NULL,
	[Pt] [numeric](3, 0) NULL,
	[MsPwr] [int] NULL,
 CONSTRAINT [UQ_Antenna] UNIQUE NONCLUSTERED 
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[assignment]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[assignment](
	[CellID] [int] NOT NULL,
	[Freq] [numeric](18, 0) NOT NULL,
 CONSTRAINT [PK_ASSIGNMENT] PRIMARY KEY CLUSTERED 
(
	[CellID] ASC,
	[Freq] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[BSC]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[BSC](
	[BscId] [int] NOT NULL,
	[MscID] [int] NOT NULL,
	[BscName] [char](30) NULL,
	[BscCompany] [char](30) NULL,
	[Longitude] [decimal](9, 6) NULL,
	[Latitude] [decimal](9, 6) NULL,
 CONSTRAINT [PK_BSC] PRIMARY KEY NONCLUSTERED 
(
	[BscId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[BTS]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[BTS](
	[BtsName] [char](30) NOT NULL,
	[Latitude] [decimal](9, 6) NULL,
	[Longitude] [decimal](9, 6) NULL,
	[Altitude] [int] NULL,
	[BtsCompany] [char](30) NULL,
	[BtsPower] [int] NULL,
	[BscId] [int] NULL,
 CONSTRAINT [PK_BTS] PRIMARY KEY NONCLUSTERED 
(
	[BtsName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[cell]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[cell](
	[CellID] [int] NOT NULL,
	[BtsName] [char](30) NOT NULL,
	[AreaName] [char](20) NULL,
	[LAC] [int] NULL,
	[Longitude] [decimal](9, 6) NULL,
	[Latitude] [decimal](9, 6) NULL,
	[Direction] [numeric](3, 0) NULL,
	[Radious] [int] NULL,
	[Bcch] [int] NULL,
 CONSTRAINT [PK_CELL] PRIMARY KEY NONCLUSTERED 
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[controll]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[controll](
	[BscId] [int] NOT NULL,
	[BtsName] [char](30) NOT NULL,
 CONSTRAINT [PK_CONTROLL] PRIMARY KEY CLUSTERED 
(
	[BscId] ASC,
	[BtsName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Cover]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Cover](
	[BtsName] [char](30) NOT NULL,
	[IMEI] [numeric](15, 0) NOT NULL,
 CONSTRAINT [PK_COVER] PRIMARY KEY NONCLUSTERED 
(
	[BtsName] ASC,
	[IMEI] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[freq]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[freq](
	[Freq] [numeric](18, 0) NOT NULL,
 CONSTRAINT [PK_FREQ] PRIMARY KEY NONCLUSTERED 
(
	[Freq] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[monitoring_data]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[monitoring_data](
	[KeyNum] [int] NOT NULL,
	[CellID] [int] NOT NULL,
	[Longitude] [decimal](9, 6) NULL,
	[Latitude] [decimal](9, 6) NULL,
	[RxLev] [decimal](10, 6) NULL,
 CONSTRAINT [PK_MONITORING_DATA] PRIMARY KEY NONCLUSTERED 
(
	[KeyNum] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[MS]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[MS](
	[IMEI] [numeric](15, 0) NOT NULL,
	[MSISDN] [numeric](11, 0) NULL,
	[UserName] [char](10) NULL,
	[MSCompany] [char](20) NULL,
	[gsmMspSense] [int] NULL,
	[gsmMsHeight] [decimal](3, 2) NULL,
	[gsmMspFout] [decimal](3, 2) NULL,
	[MZONE] [char](3) NULL,
 CONSTRAINT [PK_MS] PRIMARY KEY NONCLUSTERED 
(
	[IMEI] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[ms_busy]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[ms_busy](
	[IMEI] [numeric](15, 0) NOT NULL,
	[CellID] [int] NOT NULL,
	[MSISDN] [numeric](11, 0) NULL,
	[UserName] [char](10) NULL,
	[MSCompany] [char](20) NULL,
	[gsmMspSense] [int] NULL,
	[gsmMsHeight] [decimal](3, 2) NULL,
	[gsmMspFout] [decimal](3, 2) NULL,
	[MZONE] [char](3) NULL,
 CONSTRAINT [PK_MS_BUSY] PRIMARY KEY CLUSTERED 
(
	[IMEI] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[ms_idle]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[ms_idle](
	[IMEI] [numeric](15, 0) NOT NULL,
	[MSISDN] [numeric](11, 0) NULL,
	[UserName] [char](10) NULL,
	[MSCompany] [char](20) NULL,
	[gsmMspSense] [int] NULL,
	[gsmMsHeight] [decimal](3, 2) NULL,
	[gsmMspFout] [decimal](3, 2) NULL,
	[MZONE] [char](3) NULL,
 CONSTRAINT [PK_MS_IDLE] PRIMARY KEY CLUSTERED 
(
	[IMEI] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[MSC]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[MSC](
	[MscID] [int] NOT NULL,
	[MscName] [char](30) NULL,
	[MscCompany] [char](30) NULL,
	[MscLongitude] [decimal](9, 6) NULL,
	[MscLatitude] [decimal](9, 6) NULL,
	[MscAltitude] [int] NULL,
 CONSTRAINT [PK_MSC] PRIMARY KEY NONCLUSTERED 
(
	[MscID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[MSCT]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[MSCT](
	[MscID] [int] NOT NULL,
	[MscName] [char](30) NULL,
	[MscCompany] [char](30) NULL,
	[MscLongitude] [decimal](9, 6) NULL,
	[MscLatitude] [decimal](9, 6) NULL,
	[MscAltitude] [int] NULL,
 CONSTRAINT [PK_MSCT] PRIMARY KEY NONCLUSTERED 
(
	[MscID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[overlay]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[overlay](
	[CellID] [int] NOT NULL,
	[IMEI] [numeric](15, 0) NOT NULL,
 CONSTRAINT [PK_OVERLAY] PRIMARY KEY CLUSTERED 
(
	[CellID] ASC,
	[IMEI] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[tmp]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tmp](
	[IMEI] [numeric](15, 0) NULL,
	[MSISDN] [numeric](11, 0) NULL
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[traffic]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[traffic](
	[DATE] [int] NOT NULL,
	[TIME] [int] NOT NULL,
	[CellID] [int] NOT NULL,
	[nTCH] [int] NULL,
	[traff] [float] NULL,
	[rate] [float] NULL,
	[thtraff] [float] NULL,
	[callnum] [int] NULL,
	[congsnum] [int] NULL,
	[callcongs] [float] NULL,
 CONSTRAINT [PK_TRAFFIC] PRIMARY KEY NONCLUSTERED 
(
	[DATE] ASC,
	[TIME] ASC,
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[user]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[user](
	[uid] [int] IDENTITY(1,1) NOT NULL,
	[username] [char](16) NOT NULL,
	[password] [char](32) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[uid] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  View [dbo].[CellCallInfo]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create view [dbo].[CellCallInfo] as
	select CellID, AVG(thtraff) as Avg_thtraff, AVG(callcongs) as Avg_callcongs
	from traffic
	group by CellID
	having AVG(traff) > 23

GO
/****** Object:  View [dbo].[CellInfo]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create view [dbo].[CellInfo] as
	select CellID, AreaName, LAC, Longitude, Latitude
	from cell
	where LAC = 14121


GO
/****** Object:  View [dbo].[MSC_View]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create view [dbo].[MSC_View](MscName, MscCompany, MscLongitude, MscLatitude, MscAltitude) as 
	select MscName, MscCompany, MscLongitude, MscLatitude, MscAltitude
	from MSC

GO
/****** Object:  View [dbo].[ParMscInfo]    Script Date: 2014/7/1 21:44:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
create view [dbo].[ParMscInfo] as
select BTS.*, Antenna.*
from BSC, BTS, CELL, Antenna
where BSC.MscID = 5214 and BSC.BscId = BTS.BscId and BTS.BtsName = cell.BtsName
		and cell.CellID = Antenna.CellID

GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [IX_8]    Script Date: 2014/7/1 21:44:11 ******/
CREATE CLUSTERED INDEX [IX_8] ON [dbo].[BTS]
(
	[BscId] ASC,
	[BtsName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [adj_info]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [adj_info] ON [dbo].[adjcell]
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [configuration_FK]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [configuration_FK] ON [dbo].[Antenna]
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [ass]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [ass] ON [dbo].[assignment]
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [assignment_FK]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [assignment_FK] ON [dbo].[assignment]
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [assignment_FK2]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [assignment_FK2] ON [dbo].[assignment]
(
	[Freq] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [manage_FK]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [manage_FK] ON [dbo].[BSC]
(
	[MscID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [correspond_FK]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [correspond_FK] ON [dbo].[cell]
(
	[BtsName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [controll_FK]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [controll_FK] ON [dbo].[controll]
(
	[BscId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [controll_FK2]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [controll_FK2] ON [dbo].[controll]
(
	[BtsName] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [comefrom_FK]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [comefrom_FK] ON [dbo].[monitoring_data]
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [serving_FK]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [serving_FK] ON [dbo].[ms_busy]
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [overlay_FK]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [overlay_FK] ON [dbo].[overlay]
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [overlay_FK2]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [overlay_FK2] ON [dbo].[overlay]
(
	[IMEI] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [cong]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [cong] ON [dbo].[traffic]
(
	[DATE] ASC,
	[callcongs] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [date_c]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [date_c] ON [dbo].[traffic]
(
	[DATE] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [id_c]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [id_c] ON [dbo].[traffic]
(
	[CellID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [IX_7]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [IX_7] ON [dbo].[traffic]
(
	[congsnum] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [mix_c]    Script Date: 2014/7/1 21:44:11 ******/
CREATE NONCLUSTERED INDEX [mix_c] ON [dbo].[traffic]
(
	[CellID] ASC,
	[DATE] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
ALTER TABLE [dbo].[adjcell]  WITH CHECK ADD  CONSTRAINT [FK_ADJCELL_ADJCELL_CELL] FOREIGN KEY([CellID])
REFERENCES [dbo].[cell] ([CellID])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[adjcell] CHECK CONSTRAINT [FK_ADJCELL_ADJCELL_CELL]
GO
ALTER TABLE [dbo].[Antenna]  WITH CHECK ADD  CONSTRAINT [FK_ANTENA_EQUIP_CELL] FOREIGN KEY([CellID])
REFERENCES [dbo].[cell] ([CellID])
GO
ALTER TABLE [dbo].[Antenna] CHECK CONSTRAINT [FK_ANTENA_EQUIP_CELL]
GO
ALTER TABLE [dbo].[assignment]  WITH CHECK ADD  CONSTRAINT [FK_ASSIGNME_ASSIGNMEN_CELL] FOREIGN KEY([CellID])
REFERENCES [dbo].[cell] ([CellID])
GO
ALTER TABLE [dbo].[assignment] CHECK CONSTRAINT [FK_ASSIGNME_ASSIGNMEN_CELL]
GO
ALTER TABLE [dbo].[BSC]  WITH NOCHECK ADD  CONSTRAINT [FK_BSC_MANAGE_MSC] FOREIGN KEY([MscID])
REFERENCES [dbo].[MSC] ([MscID])
GO
ALTER TABLE [dbo].[BSC] CHECK CONSTRAINT [FK_BSC_MANAGE_MSC]
GO
ALTER TABLE [dbo].[cell]  WITH NOCHECK ADD  CONSTRAINT [FK_CELL_CORRESPON_BTS] FOREIGN KEY([BtsName])
REFERENCES [dbo].[BTS] ([BtsName])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[cell] CHECK CONSTRAINT [FK_CELL_CORRESPON_BTS]
GO
ALTER TABLE [dbo].[controll]  WITH CHECK ADD  CONSTRAINT [FK_CONTROLL_CONTROLL_BSC] FOREIGN KEY([BscId])
REFERENCES [dbo].[BSC] ([BscId])
GO
ALTER TABLE [dbo].[controll] CHECK CONSTRAINT [FK_CONTROLL_CONTROLL_BSC]
GO
ALTER TABLE [dbo].[controll]  WITH CHECK ADD  CONSTRAINT [FK_CONTROLL_CONTROLL_BTS] FOREIGN KEY([BtsName])
REFERENCES [dbo].[BTS] ([BtsName])
GO
ALTER TABLE [dbo].[controll] CHECK CONSTRAINT [FK_CONTROLL_CONTROLL_BTS]
GO
ALTER TABLE [dbo].[monitoring_data]  WITH CHECK ADD  CONSTRAINT [FK_MONITORI_COME_FROM_CELL] FOREIGN KEY([CellID])
REFERENCES [dbo].[cell] ([CellID])
GO
ALTER TABLE [dbo].[monitoring_data] CHECK CONSTRAINT [FK_MONITORI_COME_FROM_CELL]
GO
ALTER TABLE [dbo].[ms_busy]  WITH CHECK ADD  CONSTRAINT [FK_MS_BUSY_SERVING_CELL] FOREIGN KEY([CellID])
REFERENCES [dbo].[cell] ([CellID])
GO
ALTER TABLE [dbo].[ms_busy] CHECK CONSTRAINT [FK_MS_BUSY_SERVING_CELL]
GO
ALTER TABLE [dbo].[ms_busy]  WITH CHECK ADD  CONSTRAINT [FK_MS_BUSY_STATE_MS] FOREIGN KEY([IMEI])
REFERENCES [dbo].[MS] ([IMEI])
GO
ALTER TABLE [dbo].[ms_busy] CHECK CONSTRAINT [FK_MS_BUSY_STATE_MS]
GO
ALTER TABLE [dbo].[ms_idle]  WITH CHECK ADD  CONSTRAINT [FK_MS_IDLE_STATE_MS] FOREIGN KEY([IMEI])
REFERENCES [dbo].[MS] ([IMEI])
GO
ALTER TABLE [dbo].[ms_idle] CHECK CONSTRAINT [FK_MS_IDLE_STATE_MS]
GO
ALTER TABLE [dbo].[overlay]  WITH CHECK ADD  CONSTRAINT [FK_OVERLAY_OVERLAY_CELL] FOREIGN KEY([CellID])
REFERENCES [dbo].[cell] ([CellID])
GO
ALTER TABLE [dbo].[overlay] CHECK CONSTRAINT [FK_OVERLAY_OVERLAY_CELL]
GO
ALTER TABLE [dbo].[overlay]  WITH CHECK ADD  CONSTRAINT [FK_OVERLAY_OVERLAY_MS_IDLE] FOREIGN KEY([IMEI])
REFERENCES [dbo].[ms_idle] ([IMEI])
GO
ALTER TABLE [dbo].[overlay] CHECK CONSTRAINT [FK_OVERLAY_OVERLAY_MS_IDLE]
GO
ALTER TABLE [dbo].[assignment]  WITH CHECK ADD  CONSTRAINT [CHECK_FREQ] CHECK  (([Freq]>=(1) AND [Freq]<=(124)))
GO
ALTER TABLE [dbo].[assignment] CHECK CONSTRAINT [CHECK_FREQ]
GO
USE [master]
GO
ALTER DATABASE [GSM2] SET  READ_WRITE 
GO
/* 添加触发器 */
USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_Antenna]    Script Date: 2014/7/1 21:45:20 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_Antenna] on [dbo].[Antenna]
instead of insert as
update Antenna
set Antenna.AntennaHigh = inserted.AntennaHigh,Antenna.HalfPAngle = inserted.HalfPAngle,
Antenna.MaxAttenuation = inserted.MaxAttenuation,Antenna.Gain = inserted.Gain,
Antenna.AntTilt = inserted.AntTilt,Antenna.Pt = inserted.Pt,Antenna.MsPwr = inserted.MsPwr
from Antenna join inserted on Antenna.CellID = inserted.CellID
insert into Antenna (CellID,AntennaHigh,HalfPAngle,MaxAttenuation,Gain,AntTilt,Pt,MsPwr)
select inserted.CellID,inserted.AntennaHigh,inserted.HalfPAngle,inserted.MaxAttenuation,inserted.Gain,
inserted.AntTilt,inserted.Pt,inserted.MsPwr
from inserted left join Antenna on inserted.CellID = Antenna.CellID
where Antenna.CellID is null
GO


USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_assignment]    Script Date: 2014/7/1 21:46:07 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_assignment] on [dbo].[assignment]
instead of insert as 

insert into assignment(CellID,Freq)
select inserted.CellID,inserted.Freq
from inserted left join assignment on inserted.CellID = assignment.CellID and inserted.Freq = assignment.Freq
where assignment.CellID is null and assignment.Freq is null
GO


USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_BSC]    Script Date: 2014/7/1 21:46:28 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_BSC] on [dbo].[BSC]
instead of insert as
update BSC
set BSC.MscID = inserted.MscID,BSC.BscName = inserted.BscName,BSC.BscCompany = inserted.BscCompany,
BSC.Longitude = inserted.Longitude,BSC.Latitude = inserted.Latitude
from BSC join inserted on BSC.BscId = inserted.BscId

insert into BSC(BscId,MscID,BscName,BscCompany,Longitude,Latitude)
select inserted.BscId,inserted.MscID,inserted.BscName,inserted.BscCompany,inserted.Longitude,
inserted.Latitude
from inserted left join BSC on inserted.BscId = BSC.BscId
where BSC.BscId is null
GO


USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_BTS]    Script Date: 2014/7/1 21:46:40 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_BTS] on [dbo].[BTS]
instead of insert as
update BTS
set BTS.Latitude = inserted.Latitude,BTS.Longitude = inserted.Longitude,BTS.Altitude = inserted.Altitude,
BTS.BtsCompany = inserted.BtsCompany,BTS.BtsPower = inserted.BtsPower,BTS.BscId = inserted.BscId
from BTS join inserted on BTS.BtsName = inserted.BtsName

insert into BTS(BtsName,Latitude,Longitude,Altitude,BtsCompany,BtsPower,BscId)
select inserted.BtsName,inserted.Latitude,inserted.Longitude,inserted.Altitude,
inserted.BtsCompany,inserted.BtsPower,inserted.BscId
from inserted left join BTS on inserted.BtsName = BTS.BtsName
where BTS.BtsName is null
GO

USE [GSM2]
GO

/****** Object:  Trigger [dbo].[adjdis]    Script Date: 2014/7/1 21:46:57 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[adjdis] on [dbo].[cell]
after insert,update,delete as
delete 
from adjcell;

with txx as (
  select cell.CellID as ID,cell.LAC as LC,cell.Longitude as Lo,cell.Latitude as La
  from cell
)
insert into adjcell(CellID,Adj_CellID,CellLac,Adj_CellLac, distance)
select txx.ID,cell.CellID,txx.LC,cell.LAC,6371001*acos(COS(txx.La*3.14159/180)*COS(cell.Latitude*3.14159/180)*COS((txx.Lo-cell.Longitude)*3.14159/180)+SIN(txx.La*3.14159/180)*sin(cell.Latitude*3.14159/180))
from txx,cell
where txx.ID <>cell.CellID and 6371001*acos(COS(txx.La*3.14159/180)*COS(cell.Latitude*3.14159/180)*COS((txx.Lo-cell.Longitude)*3.14159/180)+SIN(txx.La*3.14159/180)*sin(cell.Latitude*3.14159/180)) < 2000
GO

USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_cell]    Script Date: 2014/7/1 21:47:13 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_cell] on [dbo].[cell]
instead of insert as 
update cell
set cell.BtsName = inserted.BtsName,cell.AreaName = inserted.AreaName,cell.LAC = inserted.LAC,
cell.Longitude = inserted.Longitude,cell.Latitude = inserted.Latitude,cell.Direction = inserted.Direction,
cell.Radious = inserted.Radious,cell.Bcch = inserted.Bcch
from cell join inserted on cell.CellID = inserted.CellID

insert into cell(CellID,BtsName,AreaName,LAC,Longitude,Latitude,Direction,Radious,Bcch)
select inserted.CellID,inserted.BtsName,inserted.AreaName,inserted.LAC,inserted.Longitude,
inserted.Latitude,inserted.Direction,inserted.Radious,inserted.Bcch
from inserted left join cell on inserted.CellID = cell.CellID
where cell.CellID is null
GO

USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_mon]    Script Date: 2014/7/1 21:47:27 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_mon] on [dbo].[monitoring_data]
instead of insert as
update monitoring_data
set monitoring_data.CellID = inserted.CellID,monitoring_data.Longitude = inserted.Longitude,
monitoring_data.Latitude = inserted.Latitude,monitoring_data.RxLev = inserted.RxLev
from monitoring_data join inserted on monitoring_data.KeyNum = inserted.KeyNum
insert into monitoring_data(KeyNum,CellID,Longitude,Latitude,RxLev)
select inserted.KeyNum,inserted.CellID,inserted.Longitude,inserted.Latitude,inserted.RxLev
from inserted left join monitoring_data on inserted.KeyNum = monitoring_data.KeyNum
where monitoring_data.KeyNum is null
GO

USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_MS]    Script Date: 2014/7/1 21:47:36 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_MS] on [dbo].[MS]
instead of insert as 
update MS 
set MS.MSISDN = inserted .MSISDN,MS.UserName = inserted.UserName,MS.MSCompany = inserted.MSCompany,
MS.gsmMspSense = inserted.gsmMspSense,MS.gsmMsHeight = inserted.gsmMsHeight,MS.gsmMspFout = inserted.gsmMspFout,
MS.MZONE = inserted.MZONE
from MS join inserted on MS.IMEI = inserted.IMEI
insert into MS (IMEI,MSISDN,UserName,MSCompany,gsmMspSense,gsmMsHeight,gsmMspFout,MZONE)
select inserted.IMEI,inserted.MSISDN,inserted.UserName,inserted.MSCompany,inserted.gsmMspSense,
inserted.gsmMsHeight,inserted.gsmMspFout,inserted.MZONE
from inserted left join MS on inserted.IMEI = MS.IMEI
where MS.IMEI is null

GO

USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_MSC]    Script Date: 2014/7/1 21:47:47 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_MSC] on [dbo].[MSC]
instead of insert as 
update MSC
set MSC.MscName = inserted.MscName,MSC.MscCompany = inserted.MscCompany,MSC.MscLongitude = inserted.MscLongitude,
MSC.MscLatitude = inserted.MscLatitude,MSC.MscAltitude = inserted.MscAltitude
from MSC join inserted on MSC.MscID = inserted.MscID

insert into MSC(MscID,MscName,MscCompany,MscLongitude,MscLatitude,MscAltitude)
select inserted.MscID,inserted.MscName,inserted.MscCompany,inserted.MscLongitude,
inserted.MscLatitude,inserted.MscAltitude
from inserted left join MSC on inserted.MscID = MSC.MscID
where MSC.MscID is null
GO

USE [GSM2]
GO

/****** Object:  Trigger [dbo].[in_traffic]    Script Date: 2014/7/1 21:48:00 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

create trigger [dbo].[in_traffic] on [dbo].[traffic]
instead of insert
as 
update traffic 
set traffic.nTCH = inserted.nTCH,traffic.traff=inserted.traff,traffic.rate = inserted.thtraff/inserted.traff,
traffic.thtraff = inserted.thtraff,traffic.callnum = inserted.callnum,traffic.congsnum=inserted.congsnum,
traffic.callcongs=CAST(inserted.congsnum as float)/inserted.callnum
from traffic join inserted on traffic.CellID = inserted.CellID and traffic.DATE = inserted.DATE and traffic.TIME = inserted.TIME and inserted.traff>0 and 
inserted.callnum>0 and inserted.congsnum >= 0 and inserted.thtraff >= 0

insert into traffic (DATE,TIME,CellID,nTCH,traff,rate,thtraff,callnum,congsnum,callcongs)
select inserted.DATE,inserted.TIME,inserted.CellID,inserted.nTCH,inserted.traff,inserted.thtraff/inserted.traff,
inserted.thtraff,inserted.callnum,inserted.congsnum,CAST(inserted.congsnum as float)/inserted.callnum
from inserted left join traffic on inserted.DATE=traffic.DATE and inserted.TIME = traffic.TIME and inserted.CellID = traffic.CellID 
where traffic.DATE is null and traffic.TIME is null and traffic.CellID is null and inserted.traff>0 and inserted.callnum>0
and inserted.congsnum >= 0 and inserted.thtraff >= 0
GO

/* 添加用户账户信息 */
USE [GSM2]
GO
insert into [user] values('admin', '0dbecd260563fcb60df7a96236926f0c')
go