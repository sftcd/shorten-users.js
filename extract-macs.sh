#!/bin/bash

# 
# Copyright (C) 2017 Tolerant Networks Limited
# 
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
# 
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
# 
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.

# pull out macs, names and classes

# you need to hand-edit the output to de-dup a few things
# and identify others based on local knowledge - with so
# few real devices, that seems easier than writing the 
# code

# in terms of numbers from my n/w that gets us from:
# users.js - 4264 lines
# output here - 62 lines
# hand edited output - 35 lines

cat $1 | awk -F\" '{print $4 "," $20 "," $16}' \
	| sed -e 's/New Device-...,/New-Device,/'  \
	| sed -e 's/New Device-..,/New-Device,/'  \
	| sort | uniq 

