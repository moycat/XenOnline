/*
 *			MoyOJ 评测程序-Cell version 0.1
 *			作者：Moycat
 *			开始时间：2015年11月10日
 *			最后更新：2015年11月11日
 */

#include <iostream>
#include <string>
#include <vector>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <time.h>
#include <unistd.h>
#include <sys/resource.h>
#include <sys/wait.h>

#define WORK_DIR "/judge/"
#define CONFIG_FILE "judge.conf"
#define BUFFER_SIZE 512
#define JAVA_TIME_RATIO 3
#define JAVA_MEMORY_RATIO 1.5
#define COMPILE_TIME_LIMIT 10
#define COMPILE_MEMORY_LIMIT 1<<30

#define NORMAL 0
#define CONFIG_ERROR -1
#define FILE_ERROR -2
#define FORK_ERROR -3
#define COMPILE_ERROR 1
#define OUT_OF_MEMORY 2
#define TIME_OUT 3
#define RUNTIME_ERROR 4

#define CPP 1
#define PASCAL 2
#define JAVA 3

using namespace std;

string config_file, summary_file, error_log;
string input_folder, output_folder;
string code_file, exe_file;

void write_error(int code, string detail, bool stop = false)
{
	freopen(summary_file.c_str(), "w", stdout);
	printf("%d\n%s", code, detail.c_str());
	fclose(stdout);
	if(stop)
	{
		exit(1);
	}
}

void write_result(int state, int test_turn, vector<int> result, vector<int> used_time, vector<int> used_memory)
{
	freopen(summary_file.c_str(), "w", stdout);
	printf("%d\n", NORMAL);
	for(int i = 0; i < test_turn; ++i)
	{
		printf("%d %d %d\n", result[i], used_time[i], used_memory[i]);
	}
}

void init(int &time_limit, int &memory_limit, int &test_turn, int &lang)
{
	string solution_floder = WORK_DIR;
	config_file = solution_floder + CONFIG_FILE;
	code_file = solution_floder + "post";
	input_folder = solution_floder + "in/";
	output_folder = solution_floder + "out/";
	summary_file = output_folder + "summary.out";
	FILE * config;
	if((config = fopen(config_file.c_str(), "r")) == NULL)
	{
		write_error(FILE_ERROR, "CONFIG FILE ERROR", true);
	}
	if(fscanf(config, "%d%d%d%d", &time_limit, &memory_limit, &test_turn, &lang) != 4)
	{
		write_error(CONFIG_ERROR, "CONFIG ERROR", true);
	}
	if(lang == JAVA)
	{
		time_limit = (double)time_limit * JAVA_TIME_RATIO;
		memory_limit = (double)memory_limit * JAVA_MEMORY_RATIO;
	}
	error_log = output_folder + "error.log";
}

int compile_cpp()
{
	int pid, state;
	exe_file = output_folder + "post.out";
	const char * compile_command[] = {"g++", code_file.c_str(), "-o", exe_file.c_str(), NULL};
	struct rlimit CPU_LIMIT, MEM_LIMIT;
	CPU_LIMIT.rlim_max = CPU_LIMIT.rlim_cur = COMPILE_TIME_LIMIT;
	MEM_LIMIT.rlim_max = MEM_LIMIT.rlim_cur = COMPILE_MEMORY_LIMIT;
	pid = fork();
	if(pid < 0)
	{
		write_error(FORK_ERROR, "FORK FAIL WHEN COMPILING", true);
	}
	if(!pid)
	{
		freopen(error_log.c_str(), "w", stderr);
		setrlimit(RLIMIT_CPU, &CPU_LIMIT);
		setrlimit(RLIMIT_AS, &MEM_LIMIT);
		alarm(COMPILE_TIME_LIMIT);
		execvp(compile_command[0], (char * const *)compile_command);
		exit(0);
	}
	else
	{
		waitpid(pid, &state, 0);
	}
	return state;
}

void compile(int lang)
{
	int result;
	switch(lang)
	{
	case CPP:
		code_file += ".cpp";
		result = compile_cpp();
		break;
//	case PASCAL:

//	case JAVA:

	}
	if(!WIFEXITED(result))
	{
		write_error(COMPILE_ERROR, "AN ERROR OCCURED WHEN COMPILING", true);
	}
}

void run(int lang, int time_limit, int memory_limit, int test_turn)
{
	const char * run_cpp[] = {exe_file.c_str()};
	const char ** run_command;
	switch(lang)
	{
	case CPP:
		run_command = run_cpp;
		break;
//	case PASCAL:

//	case JAVA:

	}
	string input = input_folder + "test";
	string output = output_folder + "out";
	vector<int> result, used_time, used_memory;
	struct rlimit CPU_LIMIT, MEM_LIMIT, THREAD_LIMIT;
	CPU_LIMIT.rlim_cur = CPU_LIMIT.rlim_max = time_limit;
	MEM_LIMIT.rlim_max = MEM_LIMIT.rlim_cur = memory_limit * 1024 * 1024;
	THREAD_LIMIT.rlim_cur = THREAD_LIMIT.rlim_max = 1;
	for(int i = 0; i < test_turn; ++i)
	{
		char now_turn[BUFFER_SIZE];
		sprintf(now_turn, "%d", i);
		string now_input = input + now_turn + ".in";
		string now_output = output + now_turn + ".out";
		int now_used_time = 0, now_used_memory = 0;
        int pid, state;
        pid = fork();
		if(pid < 0)
		{
			write_error(FORK_ERROR, "FORK FAIL WHEN RUNNING", true);
		}
		if(!pid)
		{
			freopen(now_input.c_str(), "r", stdin);
			freopen(now_output.c_str(), "w", stdout);
			freopen(error_log.c_str(), "w", stderr);
			setrlimit(RLIMIT_CPU, &CPU_LIMIT);
			setrlimit(RLIMIT_AS, &MEM_LIMIT);
			setrlimit(RLIMIT_NPROC, &THREAD_LIMIT);
			alarm(COMPILE_TIME_LIMIT);
			execvp(run_command[0], (char * const *)run_command);
			exit(0);
		}
		else
		{
			waitpid(pid, &state, 0);
			if(WIFEXITED(result))
			{
				result.push_back(NORMAL);
				//used_time.push_back();
				//used_memory.push_back();
			}
		}
	}

//	write_result(NORMAL, test_turn, result, used_time, used_memory);
}

int main(int argc, char** argv)
{
	int time_limit = 1000, memory_limit =  256, test_turn = 10, lang = CPP;
	init(time_limit, memory_limit, test_turn, lang);
	compile(lang);
	run(lang, time_limit, memory_limit, test_turn);
	return 0;
}
